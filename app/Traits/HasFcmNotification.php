<?php

namespace App\Traits;

trait HasFcmNotification
{
    /**
     * Get the device token used for FCM notifications
     */
    public function routeNotificationForFcm()
    {
        return $this->fcm_token;
    }

    /**
     * Update the device's FCM token
     */
    public function updateFcmToken($token)
    {
        $this->fcm_token = $token;
        $this->save();
        
        return $this;
    }

    /**
     * Remove the device's FCM token
     */
    public function removeFcmToken()
    {
        $this->fcm_token = null;
        $this->save();
        
        return $this;
    }
}