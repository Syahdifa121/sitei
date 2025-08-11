<?php

namespace App\Http\Controllers;

use Google_Client;
use Google_Service_Calendar;
use Google_Service_Calendar_Event;
use Google_Service_Calendar_EventReminder;
use Google_Service_Calendar_EventReminders;
use Google_Service_Oauth2;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\JadwalBimbingan;
use Carbon\Carbon;

class GoogleCalendarController extends Controller
{
    protected $client;
    protected $service;

    public function __construct()
    {
        $this->client = new Google_Client();

        try {
            $credentialsPath = storage_path('app/google-calendar/credentials.json');
            if (!file_exists($credentialsPath)) {
                throw new \Exception('Google Calendar credentials file not found');
            }

            $this->client->setAuthConfig($credentialsPath);
            $this->client->setApplicationName('Sistem Bimbingan');
            $this->client->setAccessType('offline');
            $this->client->setPrompt('consent');
            $this->client->setIncludeGrantedScopes(true);

            $this->client->addScope(Google_Service_Calendar::CALENDAR);
            $this->client->addScope('email');
            $this->client->addScope('profile');
        } catch (\Exception $e) {
            Log::error('Error in GoogleCalendarController constructor: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Mengatur callback URL berdasarkan guard yang aktif
     */
    protected function setCallbackUrl()
    {
        $guard = $this->getCurrentGuard();
        $callbackUrl = url("/$guard/google/callback");
        $this->client->setRedirectUri($callbackUrl);
        Log::info("Setting callback URL to: $callbackUrl");
    }

    /**
     * Menghubungkan dengan Google Calendar
     */
    public function connect()
    {
        try {
            $guard = $this->getCurrentGuard();
            $user = Auth::guard($guard)->user();

            if ($user->hasGoogleCalendarConnected()) {
                return $this->redirectToIndex('Anda sudah terhubung dengan Google Calendar', 'warning');
            }

            // Set state untuk validasi
            $state = base64_encode(json_encode([
                'id' => $user->getAuthIdentifier(),
                'guard' => $guard,
                'timestamp' => time()
            ]));

            $this->setCallbackUrl();

            // Tambahkan konfigurasi untuk memaksa menggunakan email yang sesuai
            $this->client->setState($state);
            $this->client->setLoginHint($user->email); // Paksa menggunakan email user
            $this->client->setPrompt('select_account consent'); // Paksa pilih akun dan minta consent
            $this->client->setHostedDomain($user->email); // Batasi domain email (opsional)

            $authUrl = $this->client->createAuthUrl();
            return redirect($authUrl);
        } catch (\Exception $e) {
            Log::error('Gagal menghubungkan ke Google Calendar: ' . $e->getMessage());
            return $this->redirectToIndex('Terjadi kesalahan saat menghubungkan ke Google Calendar', 'error');
        }
    }

    /**
     * Handle callback dari Google OAuth
     */
    public function callback(Request $request)
    {
        if (!$request->has('code')) {
            return $this->redirectToIndex('Gagal terhubung ke Google Calendar.', 'error');
        }

        try {
            $this->setCallbackUrl();
            $state = json_decode(base64_decode($request->state), true);

            if (!$state || !isset($state['guard']) || !isset($state['id'])) {
                throw new \Exception('Invalid state parameter');
            }

            $guard = $state['guard'];
            $user = Auth::guard($guard)->user();

            if ($user->getAuthIdentifier() !== $state['id']) {
                throw new \Exception('Invalid user');
            }

            if ((time() - $state['timestamp']) > 3600) {
                throw new \Exception('State expired');
            }

            $token = $this->client->fetchAccessTokenWithAuthCode($request->code);

            $this->client->setAccessToken($token);
            $oauth2 = new \Google_Service_Oauth2($this->client);
            $userInfo = $oauth2->userinfo->get();

            if ($userInfo->email !== $user->email) {
                throw new \Exception(sprintf(
                    'Email tidak sesuai. Gunakan akun Google dengan email yang terdaftar di sistem (%s)',
                    $user->email
                ));
            }

            $user->updateGoogleToken(
                $token['access_token'],
                $token['refresh_token'] ?? null,
                $token['expires_in']
            );

            // Redirect sesuai guard
            $routeName = match ($guard) {
                'dosen' => 'dosen.jadwal.index',
                'mahasiswa' => 'pilihjadwal.index',
                default => throw new \Exception('Invalid guard')
            };

            return redirect()->route($routeName, ['connected' => 'true'])
                ->with('success', 'Google Calendar berhasil terhubung!')
                ->with('first_connection', true);
        } catch (\Exception $e) {

            $errorMessage = $this->getErrorMessage($e->getMessage());

            $routeName = match (Auth::getDefaultDriver()) {
                'dosen' => 'dosen.jadwal.index',
                'mahasiswa' => 'pilihjadwal.index',
                default => 'login'
            };

            Log::error('Error Google Calendar: ' . $e->getMessage());

            return redirect()->route($routeName)
                ->with('error', $errorMesage);
        }
    }

    /**
     * Mendapatkan events dari Google Calendar
     */
    public function getEvents()
    {
        try {
            if (!$this->checkAndRefreshToken()) {
                return response()->json(['error' => 'Not authenticated'], 401);
            }

            $this->service = new Google_Service_Calendar($this->client);

            $calendarId = 'primary';
            $optParams = [
                'maxResults' => 100,
                'orderBy' => 'startTime',
                'singleEvents' => true,
                'timeMin' => Carbon::now()->startOfMonth()->toRfc3339String(),
                'timeMax' => Carbon::now()->endOfMonth()->toRfc3339String(),
            ];

            $results = $this->service->events->listEvents($calendarId, $optParams);

            $jadwalDatabase = JadwalBimbingan::all()->keyBy('event_id');

            // Format events dengan menggabungkan data dari Google Calendar dan database
            $formattedEvents = [];
            foreach ($results->getItems() as $event) {
                if (!$event->start->dateTime || !$event->end->dateTime) {
                    continue;
                }

                $eventData = [
                    'id' => $event->id,
                    'title' => $event->getSummary() ?: 'No Title',
                    'start' => Carbon::parse($event->start->dateTime)->toIso8601String(),
                    'end' => Carbon::parse($event->end->dateTime)->toIso8601String(),
                    'description' => $event->getDescription(),
                    'editable' => false,
                ];

                // Jika event ada di database, tambahkan data ekstra
                if ($jadwalDatabase->has($event->id)) {
                    $jadwal = $jadwalDatabase->get($event->id);

                    $eventData['extendedProps'] = [
                        'jenis_bimbingan' => $jadwal->jenis_bimbingan,
                        'has_kuota_limit' => $jadwal->has_kuota_limit,
                        'kuota' => $jadwal->kapasitas,
                        'catatan' => $jadwal->catatan,
                        'lokasi' => $jadwal->lokasi,
                        'status' => $jadwal->status,
                        'description' => $event->getDescription()
                    ];

                    $eventData['editable'] = true;
                    $eventData['color'] = '#4285f4';
                } else {
                    // Event eksternal (bukan jadwal bimbingan)
                    $eventData['color'] = '#9e9e9e';
                    $eventData['className'] = 'external-event';
                    $eventData['extendedProps'] = [
                        'isExternal' => true,
                        'source' => 'google'
                    ];
                }

                $formattedEvents[] = $eventData;
            }

            return response()->json($formattedEvents);
        } catch (\Exception $e) {
            Log::error('Error getting events: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Format events untuk response
     */
    protected function formatEvents($events)
    {
        $formattedEvents = [];

        foreach ($events as $event) {
            if (!$event->start->dateTime || !$event->end->dateTime) {
                continue;
            }

            $eventData = [
                'id' => $event->id,
                'title' => $event->getSummary() ?: 'No Title',
                'start' => Carbon::parse($event->start->dateTime)->toIso8601String(),
                'end' => Carbon::parse($event->end->dateTime)->toIso8601String(),
                'description' => $event->getDescription(),
                'editable' => false,
            ];

            // Custom formatting untuk event bimbingan
            if (strpos(strtolower($event->getSummary()), 'bimbingan') !== false) {
                $eventData['color'] = '#4285f4';
                $eventData['editable'] = true;
            } else {
                $eventData['color'] = '#9e9e9e';
                $eventData['className'] = 'external-event';
            }

            $formattedEvents[] = $eventData;
        }

        return response()->json($formattedEvents);
    }

    public function validateAndRefreshToken()
    {
        return $this->checkAndRefreshToken();
    }

    /**
     * Delete event from Google Calendar
     */
    public function deleteEvent($eventId)
    {
        try {
            if (!$this->checkAndRefreshToken()) {
                throw new \Exception('Not authenticated');
            }

            $this->service = new Google_Service_Calendar($this->client);
            $this->service->events->delete('primary', $eventId);

            return true;
        } catch (\Exception $e) {
            Log::error('Error deleting Google Calendar event: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Membuat event baru di Google Calendar
     */
    public function createEvent($eventData)
    {
        try {
            if (!$this->checkAndRefreshToken()) {
                throw new \Exception('Belum terautentikasi dengan Google Calendar');
            }

            $this->initializeService();

            $start = $eventData['start'];
            $end = $eventData['end'];

            // Validasi waktu
            $this->validateEventTime($start, $end);

            // Membuat objek event Google Calendar
            $event = new Google_Service_Calendar_Event([
                'summary' => $eventData['summary'],
                'description' => $eventData['description'],
                'start' => [
                    'dateTime' => $start->toRfc3339String(),
                    'timeZone' => 'Asia/Jakarta',
                ],
                'end' => [
                    'dateTime' => $end->toRfc3339String(),
                    'timeZone' => 'Asia/Jakarta',
                ]
            ]);

            if (isset($eventData['reminders'])) {
                $reminders = $this->createEventReminders($eventData['reminders']);
                $event->setReminders($reminders);
            }

            // Memasukkan event ke kalender utama
            $createdEvent = $this->service->events->insert('primary', $event);

            if (!$createdEvent) {
                throw new \Exception('Gagal membuat event di Google Calendar');
            }

            $this->logEventCreation($createdEvent);

            return $createdEvent;
        } catch (\Exception $e) {
            $this->logEventError($e, $eventData);
            throw $e;
        }
    }

    public function updateEventAttendees($eventId, $attendees)
    {
        try {
            if (!$this->checkAndRefreshToken()) {
                throw new \Exception('Tidak terautentikasi dengan Google Calendar');
            }

            $this->service = new Google_Service_Calendar($this->client);

            // Ambil event yang akan diupdate
            $event = $this->service->events->get('primary', $eventId);

            // Update attendees
            $event->setAttendees($attendees);

            // Update event dengan notifikasi
            return $this->service->events->update('primary', $eventId, $event, [
                'sendUpdates' => 'all' // Kirim notifikasi ke semua peserta
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating event attendees: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Helper Methods
     */
    protected function getCurrentGuard()
    {
        if (Auth::guard('dosen')->check()) return 'dosen';
        if (Auth::guard('mahasiswa')->check()) return 'mahasiswa';
        throw new \Exception('No valid authentication guard found');
    }

    protected function getAuthUser()
    {
        $guard = $this->getCurrentGuard();
        return Auth::guard($guard)->user();
    }

    protected function validateState($state)
    {
        if (!$state) return false;

        $user = $this->getAuthUser();
        return $state['id'] === $user->getAuthIdentifier()
            && $state['guard'] === $this->getCurrentGuard()
            && (time() - $state['timestamp']) <= 3600;
    }

    protected function redirectToIndex($message, $type)
    {
        $guard = $this->getCurrentGuard();
        if ($guard === 'mahasiswa') {
            return redirect()->route('pilihjadwal.index')
                ->with($type, $message);
        } else if ($guard === 'dosen') {
            return redirect()->route('dosen.jadwal.index')
                ->with($type, $message);
        }
        throw new \Exception('No valid authentication guard found');
    }

    protected function checkAndRefreshToken()
    {
        $user = $this->getAuthUser();

        if (!$user->hasGoogleCalendarConnected()) {
            return false;
        }

        if ($user->isGoogleTokenExpired()) {
            try {
                $currentToken = [
                    'access_token' => $user->google_access_token,
                    'refresh_token' => $user->google_refresh_token,
                    'expires_in' => $user->google_token_expires_in,
                    'created' => Carbon::parse($user->google_token_created_at)->timestamp,
                ];

                $this->client->setAccessToken($currentToken);

                if ($this->client->isAccessTokenExpired() && $user->google_refresh_token) {
                    try {
                        $newToken = $this->client->fetchAccessTokenWithRefreshToken($user->google_refresh_token);

                        // Validasi newToken
                        if (!isset($newToken['access_token'])) {
                            Log::error('New token received but no access_token present');
                            // Reset token tanpa method disconnectGoogleCalendar
                            $user->update([
                                'google_access_token' => null,
                                'google_refresh_token' => null,
                                'google_token_expires_in' => null,
                                'google_token_created_at' => null
                            ]);
                            return false;
                        }

                        // Update token
                        $user->updateGoogleToken(
                            $newToken['access_token'],
                            $newToken['refresh_token'] ?? $user->google_refresh_token,
                            $newToken['expires_in'] ?? 3600
                        );

                        $this->client->setAccessToken($newToken);
                    } catch (\Exception $e) {
                        Log::error('Error during token refresh: ' . $e->getMessage());
                        // Reset token tanpa method disconnectGoogleCalendar
                        $user->update([
                            'google_access_token' => null,
                            'google_refresh_token' => null,
                            'google_token_expires_in' => null,
                            'google_token_created_at' => null
                        ]);
                        return false;
                    }
                }
            } catch (\Exception $e) {
                Log::error('Error in checkAndRefreshToken: ' . $e->getMessage());
                return false;
            }
        } else {
            $this->client->setAccessToken([
                'access_token' => $user->google_access_token,
                'refresh_token' => $user->google_refresh_token,
                'expires_in' => $user->google_token_expires_in,
                'created' => Carbon::parse($user->google_token_created_at)->timestamp,
            ]);
        }

        return true;
    }

    /**
     * Initialize Google Calendar service
     */
    protected function initializeService()
    {
        if (!$this->service) {
            $this->service = new Google_Service_Calendar($this->client);
        }
    }

    /**
     * Validasi waktu event
     */
    protected function validateEventTime($start, $end)
    {
        Log::info('Validating event time:', [
            'start' => $start->toDateTimeString(),
            'end' => $end->toDateTimeString(),
        ]);

        // Validasi jam kerja (08:00 - 18:00)
        $startHour = $start->format('H');
        if ($startHour < 8 || $startHour >= 18) {
            throw new \Exception('Jadwal harus dalam jam kerja (08:00 - 18:00)');
        }

        // Validasi durasi minimum
        $durasi = $end->diffInMinutes($start, false);
        Log::info('Duration in minutes: ' . $durasi);

        if (abs($durasi) < 30) {
            throw new \Exception('Durasi minimum bimbingan adalah 30 menit');
        }
    }

    /**
     * Membuat pengaturan reminder untuk event
     */
    protected function createEventReminders($reminderSettings)
    {
        $reminders = new Google_Service_Calendar_EventReminders();
        $reminders->setUseDefault(false);

        $reminderOverrides = [];
        foreach ($reminderSettings['overrides'] as $override) {
            $reminder = new Google_Service_Calendar_EventReminder();
            $reminder->setMethod($override['method']);
            $reminder->setMinutes($override['minutes']);
            $reminderOverrides[] = $reminder;
        }

        $reminders->setOverrides($reminderOverrides);
        return $reminders;
    }

    /**
     * Log informasi pembuatan event
     */
    protected function logEventCreation($event)
    {
        Log::info('Successfully created Google Calendar event', [
            'event_id' => $event->id,
            'summary' => $event->summary,
            'start' => $event->start->dateTime,
            'end' => $event->end->dateTime,
            'description' => $event->description
        ]);
    }

    /**
     * Log error pembuatan event
     */
    protected function logEventError($exception, $eventData)
    {
        Log::error('Error creating Google Calendar event: ' . $exception->getMessage(), [
            'event_data' => $eventData,
            'trace' => $exception->getTraceAsString()
        ]);
    }

    protected function getErrorMessage($message)
    {
        // Cek beberapa kondisi umum
        if (str_contains($message, 'Email tidak sesuai')) {
            return 'Gunakan email yang terdaftar di sistem untuk menghubungkan Google Calendar.';
        }

        if (str_contains($message, 'invalid_grant') || str_contains($message, 'expired')) {
            return 'Silakan hubungkan ulang dengan Google Calendar.';
        }

        // Pesan default untuk error lainnya
        return 'Gagal terhubung ke Google Calendar. Silakan coba beberapa saat lagi.';
    }
}
