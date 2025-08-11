// Initialize map
const map = L.map('map').setView([-2.5489, 118.0149], 5);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap contributors'
}).addTo(map);

// Global variables
let alumniData = [];
const initialView = {
    center: [-2.5489, 118.0149],
    zoom: 5
};

// Custom icon for markers
const customIcon = L.icon({
    iconUrl: '/images/marker.png',
    iconSize: [45, 45],
    iconAnchor: [22.5, 45],
    popupAnchor: [0, -45]
});

const JOB_STATUS_MAP = {
    1: 'Bekerja',
    2: 'Wirausaha',
    3: 'Studi Lanjut',
    4: 'Mencari Kerja',
    5: 'Belum memungkinkan bekerja'
};

// Fetch alumni data from API
async function fetchAlumniData() {
    try {
        const response = await fetch('/api/alumni/map-data');
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        const data = await response.json();

        // Normalize location names to match GeoJSON format
        const JOB_STATUS_MAP = {
            1: 'Bekerja',
            2: 'Wirausaha',
            3: 'Studi Lanjut',
            4: 'Mencari Kerja',
            5: 'Belum memungkinkan bekerja'
        };
        alumniData = data.map(alumni => ({
            ...alumni,
            province: alumni.province ? alumni.province.replace(/^(KABUPATEN|KOTA)\s+/i, '').trim() : '',
            city: alumni.city ? alumni.city.replace(/^(KABUPATEN|KOTA)\s+/i, '').trim() : '',
        }));

        console.log('Alumni data loaded and normalized successfully');
        initializeData();
    } catch (error) {
        console.error('Error fetching alumni data:', error);
        initializeData(); // Initialize with empty data if fetch fails
    }
}

// Fungsi untuk mendapatkan warna berdasarkan jumlah
function getColor(count) {
    if (count === 0) return '#CCCCCC'; // abu-abu jika tidak ada data
    return count > 10 ? '#800026' :  // Merah tua
        count > 7 ? '#BD0026' :  // Merah
            count > 5 ? '#E31A1C' :  // Merah muda
                count > 3 ? '#FC4E2A' :  // Oranye
                    count > 2 ? '#FD8D3C' :  // Oranye muda
                        count > 1 ? '#FEB24C' :  // Kuning
                            '#FFEDA0';    // Kuning muda
}

// Helper untuk normalisasi nama
function normalizeName(name) {
    return (name || '').toUpperCase().trim();
}

// Marker functions
function addCustomMarker(location, alumni) {
    if (!location || !Array.isArray(location) || location.length !== 2) {
        console.warn('Invalid location for alumni:', alumni.name);
        return null;
    }

    const [lat, lng] = location;
    if (isNaN(lat) || isNaN(lng) || lat < -90 || lat > 90 || lng < -180 || lng > 180) {
        console.warn('Invalid coordinates for alumni:', alumni.name, location);
        return null;
    }

    const marker = L.marker([lat, lng], { icon: customIcon });
    marker.bindPopup(`
        <div style="width: 150px; text-align: center;">
            <img src="${alumni.photoUrl || '/images/default-avatar.jpg'}" alt="${alumni.name}" class="img-fluid" 
                style="width: 100%; height: 150px; object-fit: cover; border-radius: 10px; border: 1px solid #ddd;">
            <h4 style="font-size: 16px; font-weight: bold; margin: 8px 0 4px;">${alumni.name}</h4>
            <p style="font-size: 14px; margin: 0; color: #555;">${alumni.job || '-'}</p>
            <p style="font-size: 13px; color: #777; margin: 0;">${alumni.company || '-'}</p>
            <p style="font-size: 12px; color: #999; margin: 4px 0;">${alumni.city}, ${alumni.province}</p>
        </div>
    `);
    return marker;
}

function createClusterGroup() {
    return L.markerClusterGroup({
        iconCreateFunction: function (cluster) {
            const count = cluster.getChildCount();
            return L.divIcon({
                html: `
                    <div style="position: relative; width: 45px; height: 45px;">
                        <img src="/images/cluster-icon.png" style="width: 100%; height: 100%;">
                        <span style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; 
                                    display: flex; align-items: center; justify-content: center; 
                                    color: white; font-weight: bold; font-size: 20px; text-shadow: 
                                    -1px -1px 0 #000, 1px -1px 0 #000, -1px 1px 0 #000, 1px 1px 0 #000;">
                            ${count}
                        </span>
                    </div>`,
                className: 'custom-cluster-icon',
                iconSize: [45, 45]
            });
        },
        maxClusterRadius: 20
    });
}

// Layer functions
function createCityLayer(data) {
    return L.geoJSON(null, {
        pane: 'polygons',
        style: function (feature) {
            const cityKey = `${normalizeName(feature.properties.NAME_1)}-${normalizeName(feature.properties.NAME_2)}`;
            const count = calculateAlumniCount(data, 'city', cityKey);
            return {
                fillColor: getColor(count),
                weight: 1,
                opacity: 1,
                color: 'white',
                fillOpacity: 0.7
            };
        },
        onEachFeature: function (feature, layer) {
            const cityKey = `${normalizeName(feature.properties.NAME_1)}-${normalizeName(feature.properties.NAME_2)}`;
            const count = calculateAlumniCount(data, 'city', cityKey);
            layer.bindPopup(
                '<strong>' + feature.properties.NAME_2 + ', ' + feature.properties.NAME_1 + '</strong><br>' +
                'Jumlah Alumni: ' + count
            );
            layer.on({
                mouseover: highlightCity,
                mouseout: function (e) {
                    if (e.target !== window.activeLayer) {
                        e.target.setStyle({
                            weight: 1,
                            color: 'white'
                        });
                    }
                },
                click: function (e) {
                    if (window.activeLayer) {
                        window.activeLayer.setStyle({
                            weight: 1,
                            color: 'white'
                        });
                    }
                    window.activeLayer = e.target;
                    e.target.setStyle({
                        weight: 3,
                        color: '#000'
                    });
                    e.target.bringToFront();
                }
            });
        }
    });
}

function createProvinceLayer(data) {
    return L.geoJSON(null, {
        style: function (feature) {
            const count = calculateAlumniCount(data, 'province', feature.properties.NAME_1);
            return {
                fillColor: getColor(count),
                weight: 1,
                opacity: 1,
                color: 'white',
                fillOpacity: 0.7
            };
        },
        onEachFeature: function (feature, layer) {
            const count = calculateAlumniCount(data, 'province', feature.properties.NAME_1);
            layer.bindPopup(
                '<strong>' + feature.properties.NAME_1 + '</strong><br>' +
                'Jumlah Alumni: ' + count
            );
            layer.on({
                mouseover: highlightProvince,
                mouseout: function (e) {
                    if (e.target !== window.activeLayer) {
                        e.target.setStyle({
                            weight: 1,
                            color: 'white'
                        });
                    }
                },
                click: function (e) {
                    if (window.activeLayer) {
                        window.activeLayer.setStyle({
                            weight: 1,
                            color: 'white'
                        });
                    }
                    window.activeLayer = e.target;
                    e.target.setStyle({
                        weight: 3,
                        color: '#000'
                    });
                    e.target.bringToFront();
                }
            });
        }
    });
}

// Calculation functions
function calculateAlumniCount(data, type, key) {
    if (!data || !Array.isArray(data)) {
        console.warn('Invalid data provided to calculateAlumniCount');
        return 0;
    }

    if (type === 'city') {
        const [province, city] = key.split('-');
        if (!province || !city) {
            console.warn('Invalid city key format:', key);
            return 0;
        }

        const count = data.filter(a =>
            normalizeName(a.province) === normalizeName(province) &&
            normalizeName(a.city) === normalizeName(city)
        ).length;

        console.log(`Calculating city count for ${province}-${city}:`, count);
        return count;
    } else {
        if (!key) {
            console.warn('Invalid province key');
            return 0;
        }

        const count = data.filter(a =>
            normalizeName(a.province) === normalizeName(key)
        ).length;

        console.log(`Calculating province count for ${key}:`, count);
        return count;
    }
}

function calculateAlumniPerCity(data) {
    const result = {};
    data.forEach(alumni => {
        const key = `${normalizeName(alumni.province)}-${normalizeName(alumni.city)}`;
        if (!result[key]) {
            result[key] = { count: 0, alumni: [] };
        }
        result[key].count++;
        result[key].alumni.push(alumni);
    });
    return result;
}

// UI functions
function highlightProvince(layer) {
    layer.setStyle({
        weight: 2,
        color: '#666'
    });
    layer.bringToFront();
}

function highlightCity(layer) {
    layer.setStyle({
        weight: 2,
        color: '#666'
    });
    layer.bringToFront();
}

function showCityLayer(provinceName) {
    if (window.cityLayer) {
        map.removeLayer(window.cityLayer);
    }
    if (window.provinceLayer) {
        map.removeLayer(window.provinceLayer);
    }
    window.cityLayer = createCityLayer(alumniData);
    $.getJSON('/geojson/kota.geojson', function (kotaData) {
        const filteredData = kotaData.features.filter(feature =>
            feature.properties.NAME_1 === provinceName
        );
        window.cityLayer.addData({
            type: 'FeatureCollection',
            features: filteredData
        });
        map.addLayer(window.cityLayer);
    });
}

function renderTable(data) {
    const tableBody = document.getElementById('alumniTableBody');
    if (!tableBody) return;

    tableBody.innerHTML = '';
    data = data.slice().sort((a, b) => (a.name || '').localeCompare(b.name || ''));
    
    if (data.length === 0) {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td colspan="8" class="text-center">Tidak ada data</td>
        `;
        tableBody.appendChild(row);
        return;
    }

    data.forEach((alumni, index) => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td class="text-center">${index + 1}</td>
            <td class="text-center">${alumni.name || '-'}</td>
            <td class="text-center">${alumni.job || '-'}</td>
            <td class="text-center">${alumni.company || '-'}</td>
            <td class="text-center">${alumni.province || '-'}</td>
            <td class="text-center">${alumni.city || '-'}</td>
            <td class="text-center">${alumni.graduationYear || '-'}</td>
            <td class="text-center">
                <div class="d-flex gap-2 justify-content-center">
                    <button type="button" class="btn btn-info btn-sm btn-detail-alumni"
                        data-nama="${escapeHtml(alumni.name || '')}"
                        data-email="${escapeHtml(alumni.email || '')}"
                        data-nim="${escapeHtml(alumni.nim || '')}"
                        data-tahun-lulus="${escapeHtml(alumni.graduationYear || '')}"
                        data-no-telepon="${escapeHtml(alumni.phone || '')}"
                        data-alamat="${escapeHtml(alumni.address || '')}"
                        data-nama-perusahaan="${escapeHtml(alumni.company || '')}"
                        data-provinsi="${escapeHtml(alumni.province || '')}"
                        data-kota="${escapeHtml(alumni.city || '')}"
                        data-created-at="${escapeHtml(alumni.createdAt || '')}">
                        <i class="fas fa-info-circle"></i>
                    </button>
                </div>
            </td>
        `;
        tableBody.appendChild(row);
        renderTable(data);
    });
}

function initializeSelect2() {
    // Terapkan select2 ke semua dropdown filter
    $('#filterName, #companySelect, #yearSelect, #provinceSelect, #citySelect, #jobStatusSelect, #jobSelect').select2({
        placeholder: 'Pilih...',
        allowClear: true,
        width: '100%'
    });
}

function populateDropdowns() {
    const companies = [...new Set(alumniData.map(a => a.company))].filter(Boolean).sort((a, b) => a.localeCompare(b));
    const years = [...new Set(alumniData.map(a => a.graduationYear))].sort((a, b) => a.localeCompare(b));
    const provinces = [...new Set(alumniData.map(a => a.province))].filter(Boolean).sort((a, b) => a.localeCompare(b));
    const cities = [...new Set(alumniData.map(a => a.city))].filter(Boolean).sort((a, b) => a.localeCompare(b));
    const jobStatuses = [...new Set(alumniData.map(a => a.job_status))].filter(Boolean).sort((a, b) => a.localeCompare(b));
    const jobs = [...new Set(alumniData.map(a => a.job))].filter(Boolean).sort((a, b) => a.localeCompare(b));

    $('#companySelect').empty().append('<option value="">Semua Perusahaan</option>');
    $('#yearSelect').empty().append('<option value="">Semua Tahun</option>');
    $('#provinceSelect').empty().append('<option value="">Semua Provinsi</option>');
    $('#citySelect').empty().append('<option value="">Semua Kota</option>');
    $('#jobStatusSelect').empty().append('<option value="">Semua Status</option>');
    $('#jobSelect').empty().append('<option value="">Semua Pekerjaan</option>');

    companies.forEach(company => {
        $('#companySelect').append(`<option value="${company}">${company}</option>`);
    });
    years.forEach(year => {
        $('#yearSelect').append(`<option value="${year}">${year}</option>`);
    });
    provinces.forEach(province => {
        $('#provinceSelect').append(`<option value="${province}">${province}</option>`);
    });
    cities.forEach(city => {
        $('#citySelect').append(`<option value="${city}">${city}</option>`);
    });
    jobStatuses.forEach(status => {
        $('#jobStatusSelect').append(`<option value="${status}">${status}</option>`);
    });
    jobs.forEach(job => {
        $('#jobSelect').append(`<option value="${job}">${job}</option>`);
    });

    $('#citySelect').prop('disabled', true);

    const names = [...new Set(alumniData.map(a => a.name))].filter(Boolean).sort((a, b) => a.localeCompare(b));
    $('#filterName').empty().append('<option value="">Semua Nama</option>');
    names.forEach(name => {
        $('#filterName').append(`<option value="${name}">${name}</option>`);
    });
}

function filterAlumni() {
    const nameFilter = $('#filterName').val().toLowerCase();
    const companyFilter = $('#companySelect').val();
    const yearFilter = $('#yearSelect').val();
    const provinceFilter = $('#provinceSelect').val();
    const cityFilter = $('#citySelect').val();
    const jobStatusFilter = $('#jobStatusSelect').val();
    const jobFilter = $('#jobSelect').val();
    const genderFilter = document.querySelector('input[name="genderFilter"]:checked')?.value;

    const filteredData = alumniData.filter(alumni => {
        return (!nameFilter || alumni.name.toLowerCase().includes(nameFilter)) &&
            (!companyFilter || alumni.company === companyFilter) &&
            (!yearFilter || alumni.graduationYear === yearFilter) &&
            (!provinceFilter || alumni.province === provinceFilter) &&
            (!cityFilter || alumni.city === cityFilter) &&
            (!jobStatusFilter || alumni.job_status === jobStatusFilter) &&
            (!jobFilter || alumni.job === jobFilter) &&
            (!genderFilter || alumni.gender === genderFilter);
    });

    renderTable(filteredData);
    updateMap(filteredData);
}

// Helper function untuk mendapatkan jumlah alumni
function getAlumniCount(feature, type) {
    // Gunakan data yang sedang aktif (filteredData) bukan alumniData global
    const currentData = window.currentFilteredData || alumniData;

    if (type === 'city') {
        // Gunakan NAME_2 untuk kota/kabupaten dan NAME_1 untuk provinsi
        const cityName = normalizeName(feature.properties.NAME_2);
        const provinceName = normalizeName(feature.properties.NAME_1);

        // Hitung jumlah alumni yang provinsi dan kotanya cocok
        const count = currentData.filter(a =>
            normalizeName(a.province) === provinceName &&
            normalizeName(a.city) === normalizeName(cityName)
        ).length;

        console.log(`City count for ${cityName}, ${provinceName}:`, count);
        return count;
    } else if (type === 'working') {
        const cityName = normalizeName(feature.properties.NAME_2);
        const provinceName = normalizeName(feature.properties.NAME_1);

        const count = currentData.filter(a =>
            normalizeName(a.province) === provinceName &&
            normalizeName(a.city) === cityName &&
            a.job_status === 'Bekerja'
        ).length;

        console.log(`Working count for ${cityName}, ${provinceName}:`, count);
        return count;
    } else if (type === 'not_working') {
        const cityName = normalizeName(feature.properties.NAME_2);
        const provinceName = normalizeName(feature.properties.NAME_1);

        const count = currentData.filter(a =>
            normalizeName(a.province) === provinceName &&
            normalizeName(a.city) === cityName &&
            a.job_status === 'Tidak Bekerja'
        ).length;

        console.log(`Not working count for ${cityName}, ${provinceName}:`, count);
        return count;
    } else {
        // Untuk provinsi, gunakan NAME_1
        const provinceName = normalizeName(feature.properties.NAME_1);
        const count = currentData.filter(a =>
            normalizeName(a.province) === provinceName
        ).length;

        console.log(`Province count for ${provinceName}:`, count);
        return count;
    }
}

// Update calculateAlumniCountByStatus to match the new structure
function calculateAlumniCountByStatus(data, locationName, status) {
    if (!data || !Array.isArray(data)) {
        console.warn('Invalid data provided to calculateAlumniCountByStatus');
        return 0;
    }

    // Check if locationName is a province name
    const isProvince = data.some(a => normalizeName(a.province) === normalizeName(locationName));

    if (isProvince) {
        // Count by province
        const count = data.filter(a =>
            normalizeName(a.province) === normalizeName(locationName) &&
            a.job_status === status
        ).length;

        console.log(`Province status count for ${locationName} (${status}):`, count);
        return count;
    } else {
        // Count by city
        const count = data.filter(a =>
            normalizeName(a.city) === normalizeName(locationName) &&
            a.job_status === status
        ).length;

        console.log(`City status count for ${locationName} (${status}):`, count);
        return count;
    }
}

// Update updateMap function to use new layers
function updateMap(filteredData) {
    // Store current filtered data for use in getAlumniCount
    window.currentFilteredData = filteredData;

    // Re-trigger active category or area layer so counts update
    const activeCategory = document.querySelector('.category-checkbox:checked');
    const activeArea = document.querySelector('input[name="areaLayer"]:checked');
    if (activeCategory) {
        activeCategory.dispatchEvent(new Event('change'));
    } else if (activeArea) {
        activeArea.dispatchEvent(new Event('change'));
    }

    // Update markers
    if (window.alumniMarkersCluster) {
        map.removeLayer(window.alumniMarkersCluster);
    }

    // Create new cluster group
    window.alumniMarkersCluster = createClusterGroup();

    // Add alumni markers
    filteredData.forEach(function (alumni) {
        const marker = addCustomMarker(alumni.location, alumni);
        if (marker) {
            window.alumniMarkersCluster.addLayer(marker);
        }
    });

    // Add markers if toggle is active
    const markerToggle = document.querySelector('#markerToggle');
    if (!markerToggle || markerToggle.checked) {
        map.addLayer(window.alumniMarkersCluster);
    }

    // Remove legend update from here
    if (window.legend) {
        map.removeControl(window.legend);
        window.legend = null;
    }
}

// Update initializeData to include new layers
function initializeData() {
    try {
        // Remove existing legends
        if (window.legend) {
            map.removeControl(window.legend);
            window.legend = null;
        }
        document.querySelectorAll('.info.legend').forEach(function (legendElement) {
            if (legendElement && legendElement.parentNode) {
                legendElement.parentNode.removeChild(legendElement);
            }
        });

        console.log('Initializing dropdowns...');
        populateDropdowns();

        console.log('Initializing Select2...');
        initializeSelect2();

        console.log('Initializing table...');
        renderTable(alumniData);

        console.log('Initializing map markers...');
        updateMap(alumniData);

        // Add marker toggle control
        L.control.markerToggle().addTo(map);

        // Add layer control
        L.control.layerControl().addTo(map);

        map.addLayer(window.alumniMarkersCluster);
        console.log('Initialization complete');
    } catch (error) {
        console.error('Error during initialization:', error);
    }
}

// Event listeners
$('#filterName').on('change', filterAlumni);
$('#companySelect').on('change', filterAlumni);
$('#yearSelect').on('change', filterAlumni);
$('#provinceSelect').on('change', function () {
    const selectedProvince = $(this).val();
    const cityDropdown = $('#citySelect');
    cityDropdown.empty().append('<option value="">Semua Kota</option>');
    if (!selectedProvince) {
        cityDropdown.prop('disabled', true);
        return;
    }
    // Filter alumniData untuk kota di provinsi terpilih
    const cities = [...new Set(alumniData.filter(a => a.province === selectedProvince).map(a => a.city))].filter(Boolean);
    if (cities.length > 0) {
        cityDropdown.prop('disabled', false);
        cities.forEach(city => {
            cityDropdown.append(`<option value="${city}">${city}</option>`);
        });
    } else {
        cityDropdown.prop('disabled', true);
    }
});
$('#jobStatusSelect').on('change', filterAlumni);
$('#jobSelect').on('change', filterAlumni);

// Filter popup functionality
document.getElementById("filterButton").addEventListener("click", function () {
    const filterPopup = document.getElementById("filterPopup");
    filterPopup.style.display = filterPopup.style.display === "none" ? "block" : "none";
});

document.getElementById("closeFilterPopup").addEventListener("click", function () {
    document.getElementById("filterPopup").style.display = "none";
});

document.getElementById("resetButton").addEventListener("click", function (e) {
    e.preventDefault();
    resetFilters();
});

document.getElementById("filterButton2").addEventListener("click", function () {
    filterAlumni();
    document.getElementById("filterPopup").style.display = "none";
});

// Load GeoJSON data and initialize layers
$.getJSON('/geojson/kota.geojson', function (kotaData) {
    window.cityLayer = createCityLayer(alumniData);
    window.cityLayer.addData(kotaData);
    map.addLayer(window.cityLayer);
});

$.getJSON('/geojson/provinsi.geojson', function (provinsiData) {
    // Create province layer
    window.provinceLayer = createProvinceLayer(alumniData);
    window.provinceLayer.addData(provinsiData);

    // Add province layer to map if it's selected
    const provinceRadio = document.querySelector('input[name="layerControl"][id="provinceLayer"]');
    if (provinceRadio && provinceRadio.checked) {
        map.addLayer(window.provinceLayer);
    }
});

// Start the application
fetchAlumniData();

// Add global CSS for control z-index management
const style = document.createElement('style');
style.textContent = `
    .leaflet-top.leaflet-right {
        z-index: 1000 !important;
    }
    .leaflet-control-container .leaflet-control {
        margin-bottom: 5px !important;
    }
    .leaflet-bar.leaflet-control {
        box-shadow: 0 2px 8px rgba(0,0,0,0.25) !important;
        border: 1px solid #e0e0e0 !important;
    }
    .info.legend {
        clear: both !important;
        margin-top: 5px !important;
    }
`;
document.head.appendChild(style);

// Custom Layer Control
L.Control.LayerControl = L.Control.extend({
    options: { position: 'topright' },
    onAdd: function (map) {
        const container = L.DomUtil.create('div', 'leaflet-bar leaflet-control');
        container.style.backgroundColor = 'rgba(255, 255, 255, 0.95)';
        container.style.padding = '12px';
        container.style.borderRadius = '8px';
        container.style.boxShadow = '0 2px 10px rgba(0,0,0,0.3)';
        container.style.marginBottom = '10px';
        container.style.border = '2px solid #ffffff';
        container.style.zIndex = '1000';
        container.style.position = 'relative';

        const button = L.DomUtil.create('div', '', container);
        button.innerHTML = '<i class="fas fa-layer-group"></i>';
        button.style.fontSize = '16px';
        button.style.color = '#555';
        button.style.textAlign = 'center';
        button.style.width = '100%';
        button.style.height = '100%';
        button.style.display = 'flex';
        button.style.justifyContent = 'center';
        button.style.alignItems = 'center';

        const content = L.DomUtil.create('div', '', container);
        content.style.display = 'none';
        content.style.position = 'absolute';
        content.style.top = '45px';
        content.style.right = '0';
        content.style.backgroundColor = 'white';
        content.style.padding = '10px';
        content.style.borderRadius = '4px';
        content.style.boxShadow = '0 1px 5px rgba(0,0,0,0.4)';
        content.style.zIndex = '1000';
        content.style.width = '200px';

        L.DomEvent.on(container, 'click', function (e) {
            L.DomEvent.stopPropagation(e);
            content.style.display = content.style.display === 'none' ? 'block' : 'none';
        });

        L.DomEvent.on(document, 'click', function () {
            content.style.display = 'none';
        });

        L.DomEvent.disableClickPropagation(container);
        L.DomEvent.disableScrollPropagation(container);

        // Box 1: Area
        const areaBox = L.DomUtil.create('div', '', content);
        areaBox.style.marginBottom = '15px';
        areaBox.style.paddingBottom = '10px';
        areaBox.style.borderBottom = '1px solid #eee';

        const areaTitle = L.DomUtil.create('div', '', areaBox);
        areaTitle.innerHTML = '<strong>Area</strong>';
        areaTitle.style.marginBottom = '8px';

        const areaLayers = [
            { name: 'Provinsi', layer: 'provinceLayer', type: 'province' },
            { name: 'Kota', layer: 'cityLayer', type: 'city' }
        ];

        areaLayers.forEach(layer => {
            const div = L.DomUtil.create('div', '', areaBox);
            div.style.marginBottom = '5px';
            div.style.display = 'flex';
            div.style.alignItems = 'center';

            const checkbox = L.DomUtil.create('input', '', div);
            checkbox.type = 'checkbox';
            checkbox.name = 'areaLayer';
            checkbox.id = layer.layer;
            checkbox.className = 'area-checkbox';
            checkbox.style.marginRight = '5px';

            const label = L.DomUtil.create('label', '', div);
            label.htmlFor = layer.layer;
            label.innerHTML = layer.name;
            label.style.marginLeft = '5px';
            label.style.fontSize = '12px';

            L.DomEvent.on(checkbox, 'change', function (e) {
                const isChecked = e.target.checked;
                // Uncheck all other area checkboxes if this one is checked
                if (isChecked) {
                    const allCheckboxes = div.parentNode.querySelectorAll('.area-checkbox');
                    allCheckboxes.forEach(cb => {
                        if (cb !== checkbox) {
                            cb.checked = false;
                        }
                    });
                    // Reset/uncheck semua kategori data saat pindah area
                    const allCategoryCheckboxes = document.querySelectorAll('.category-checkbox');
                    allCategoryCheckboxes.forEach(cb => {
                        cb.checked = false;
                    });

                    // Hapus semua layer yang ada
                    ['jobStatusLayer', 'intersectionLayer', 'alumniCountLayer', 'companyTypeLayer', 'salaryLayer', 'jobLayer'].forEach(l => {
                        if (window[l]) {
                            map.removeLayer(window[l]);
                            window[l] = null;
                        }
                    });

                    // Reset legend
                    if (window.legend) {
                        map.removeControl(window.legend);
                        window.legend = null;
                    }

                    // Reset validation
                    validateCombinations();

                    // Buat ulang legend setelah area berubah
                    createAndUpdateLegend();

                    console.log('Area switched to:', layer.type, '- All categories reset');
                }
                // Trigger the same logic as before, but only if checked
                if (isChecked) {
                    if (window.choroplethLayer) {
                        map.removeLayer(window.choroplethLayer);
                        window.choroplethLayer = null;
                    }
                    if (window.provinceLayer) {
                        map.removeLayer(window.provinceLayer);
                        window.provinceLayer = null;
                    }
                    if (window.cityLayer) {
                        map.removeLayer(window.cityLayer);
                        window.cityLayer = null;
                    }
                    if (window.alumniCountLayer) {
                        map.removeLayer(window.alumniCountLayer);
                        window.alumniCountLayer = null;
                    }
                    // Tidak perlu menampilkan layer apapun sampai user pilih kategori
                }
            });
        });

        // Box 2: Jenis Kelamin
        const genderBox = L.DomUtil.create('div', '', content);
        genderBox.style.marginBottom = '15px';
        genderBox.style.paddingBottom = '10px';
        genderBox.style.borderBottom = '1px solid #eee';

        const genderTitle = L.DomUtil.create('div', '', genderBox);
        genderTitle.innerHTML = '<strong>Jenis Kelamin</strong>';
        genderTitle.style.marginBottom = '8px';

        const genderOptions = [
            { name: 'Laki-laki', value: 'Laki-laki' },
            { name: 'Perempuan', value: 'Perempuan' }
        ];

        genderOptions.forEach(option => {
            const div = L.DomUtil.create('div', '', genderBox);
            div.style.marginBottom = '5px';
            div.style.display = 'flex';
            div.style.alignItems = 'center';

            const checkbox = L.DomUtil.create('input', '', div);
            checkbox.type = 'checkbox';
            checkbox.name = 'genderFilter';
            checkbox.value = option.value;
            checkbox.className = 'gender-checkbox';
            checkbox.style.marginRight = '5px';

            const label = L.DomUtil.create('label', '', div);
            label.innerHTML = option.name;
            label.style.marginLeft = '5px';
            label.style.fontSize = '12px';

            L.DomEvent.on(checkbox, 'change', function (e) {
                if (e.target.checked) {
                    const allCheckboxes = genderBox.querySelectorAll('.gender-checkbox');
                    allCheckboxes.forEach(cb => {
                        if (cb !== checkbox) cb.checked = false;
                    });
                }
                filterAlumni();
            });
        });

        // Box 3: Data Categories
        const categoryBox = L.DomUtil.create('div', '', content);
        const categoryTitle = L.DomUtil.create('div', '', categoryBox);
        categoryTitle.innerHTML = '<strong>Kategori Data</strong>';
        categoryTitle.style.marginBottom = '8px';

        // Tombol Reset
        const resetButton = L.DomUtil.create('button', '', categoryBox);
        resetButton.innerHTML = '🔄 Reset Semua';
        resetButton.style.width = '100%';
        resetButton.style.padding = '6px 8px';
        resetButton.style.marginBottom = '10px';
        resetButton.style.backgroundColor = '#f8f9fa';
        resetButton.style.border = '1px solid #dee2e6';
        resetButton.style.borderRadius = '4px';
        resetButton.style.fontSize = '11px';
        resetButton.style.cursor = 'pointer';
        resetButton.style.transition = 'all 0.2s';

        // Hover effects
        resetButton.addEventListener('mouseenter', function () {
            this.style.backgroundColor = '#e9ecef';
            this.style.borderColor = '#adb5bd';
        });
        resetButton.addEventListener('mouseleave', function () {
            this.style.backgroundColor = '#f8f9fa';
            this.style.borderColor = '#dee2e6';
        });

        L.DomEvent.on(resetButton, 'click', function (e) {
            L.DomEvent.stopPropagation(e);
            const allCategoryCheckboxes = document.querySelectorAll('.category-checkbox');
            allCategoryCheckboxes.forEach(cb => { cb.checked = false; });
            ['jobStatusLayer', 'intersectionLayer', 'alumniCountLayer', 'companyTypeLayer', 'salaryLayer', 'jobLayer'].forEach(l => {
                if (window[l]) { map.removeLayer(window[l]); window[l] = null; }
            });
            if (window.legend) { map.removeControl(window.legend); window.legend = null; }
            validateCombinations();

            // Buat ulang legend setelah reset
            createAndUpdateLegend();
            console.log('All categories reset via button');
        });

        L.DomEvent.disableClickPropagation(resetButton);

        const categories = [
            { name: 'Jumlah Alumni', type: 'count' },
            { name: 'Jenis Perusahaan', type: 'company' },
            { name: 'Penghasilan Alumni', type: 'salary' },
            { name: 'Status Pekerjaan', type: 'job_status' }, // NEW
            { name: 'Pekerjaan', type: 'job' } // PEKERJAAN SAAT INI
        ];

        // Simpan referensi checkbox kategori
        const categoryCheckboxRefs = {};

        categories.forEach(category => {
            const div = L.DomUtil.create('div', '', categoryBox);
            div.style.marginBottom = '5px';
            div.style.display = 'flex';
            div.style.alignItems = 'center';

            const checkbox = L.DomUtil.create('input', '', div);
            checkbox.type = 'checkbox';
            checkbox.id = category.layer;
            checkbox.value = category.type; // Set value ke type kategori
            checkbox.className = 'category-checkbox';
            checkbox.style.marginRight = '5px';
            categoryCheckboxRefs[category.type] = checkbox;

            const label = L.DomUtil.create('label', '', div);
            label.htmlFor = category.layer;
            label.innerHTML = category.name;
            label.style.marginLeft = '5px';
            label.style.fontSize = '12px';

            L.DomEvent.on(checkbox, 'change', function (e) {
                validateCombinations();
                updateIntersectionLayer();

                // Update legend setelah kategori berubah
                if (window.legend && window.legend.update) {
                    window.legend.update();
                }
            });
        });

        // Validasi kombinasi kategori
        function validateCombinations() {
            const checkedCategories = [];
            document.querySelectorAll('.category-checkbox').forEach((cb, idx) => {
                if (cb.checked) checkedCategories.push(categories[idx].type);
            });

            // Reset semua style
            document.querySelectorAll('.category-checkbox').forEach((cb, idx) => {
                const parentDiv = cb.parentElement;
                parentDiv.style.opacity = '1';
                parentDiv.style.pointerEvents = 'auto';
                parentDiv.title = '';
            });

            // Jika tidak ada yang dipilih, semua aktif
            if (checkedCategories.length === 0) {
                return;
            }

            // Definisi kombinasi yang diizinkan
            const allowedCombinations = [
                // Single kategori - semua diizinkan
                ['count'], ['company'], ['salary'], ['job_status'], ['job'],

                // Dua kategori - job_status hanya sendiri
                ['company', 'salary'],
                ['job', 'salary'], ['job', 'company'],

                // Tiga kategori - job_status tidak termasuk
                ['job', 'company', 'salary']
            ];

            // Cek apakah kombinasi saat ini diizinkan
            const currentCombination = checkedCategories.sort();
            const isValidCombination = allowedCombinations.some(allowed =>
                allowed.length === currentCombination.length &&
                allowed.sort().every((item, index) => item === currentCombination[index])
            );

            // Jika kombinasi tidak valid, disable kategori yang tidak kompatibel
            if (checkedCategories.length > 0) {
                document.querySelectorAll('.category-checkbox').forEach((cb, idx) => {
                    if (!cb.checked) {
                        const testCombination = [...checkedCategories, categories[idx].type].sort();
                        const wouldBeValid = allowedCombinations.some(allowed =>
                            allowed.length === testCombination.length &&
                            allowed.sort().every((item, index) => item === testCombination[index])
                        );

                        if (!wouldBeValid) {
                            const parentDiv = cb.parentElement;
                            parentDiv.style.opacity = '0.5';
                            parentDiv.style.pointerEvents = 'none';
                            parentDiv.title = 'Kombinasi ini tidak tersedia';
                        }
                    }
                });
            }

            // Tambahkan info kombinasi yang sedang aktif
            let infoDiv = document.getElementById('combination-info');
            if (!infoDiv) {
                infoDiv = L.DomUtil.create('div', '', categoryBox);
                infoDiv.id = 'combination-info';
                infoDiv.style.marginTop = '10px';
                infoDiv.style.padding = '8px';
                infoDiv.style.borderRadius = '4px';
                infoDiv.style.fontSize = '11px';
            }

            if (checkedCategories.length === 0) {
                infoDiv.innerHTML = '<span style="color:#666;">💡 Pilih kategori untuk melihat peta tematik</span>';
                infoDiv.style.backgroundColor = '#f8f9fa';
                infoDiv.style.border = '1px solid #e9ecef';
            } else if (isValidCombination) {
                const combinationNames = checkedCategories.map(type =>
                    categories.find(cat => cat.type === type)?.name || type
                ).join(' + ');
                infoDiv.innerHTML = `<span style="color:#28a745;">✓ ${combinationNames}</span>`;
                infoDiv.style.backgroundColor = '#d4edda';
                infoDiv.style.border = '1px solid #c3e6cb';
            } else {
                infoDiv.innerHTML = '<span style="color:#dc3545;">⚠️ Kombinasi tidak tersedia</span>';
                infoDiv.style.backgroundColor = '#f8d7da';
                infoDiv.style.border = '1px solid #f5c6cb';
            }
        }

        // Inisialisasi validasi
        validateCombinations();

        // Fungsi utama untuk update layer interseksi
        function updateIntersectionLayer() {
            console.log('updateIntersectionLayer called');
            // Hapus semua layer choropleth yang mungkin masih aktif
            ['jobStatusLayer', 'intersectionLayer', 'alumniCountLayer', 'companyTypeLayer', 'salaryLayer', 'jobLayer'].forEach(l => {
                if (window[l]) {
                    map.removeLayer(window[l]);
                    window[l] = null;
                }
            });

            // Ambil area yang dipilih
            const selectedArea = document.querySelector('input[name="areaLayer"]:checked');
            if (!selectedArea) return;
            const areaType = selectedArea.id === 'cityLayer' ? 'city' : 'province';

            // Ambil kategori yang aktif
            const activeCategories = categories.filter(cat => categoryCheckboxRefs[cat.type] && categoryCheckboxRefs[cat.type].checked);
            console.log('Active categories:', activeCategories.map(c => c.type));
            if (activeCategories.length === 0) return;

            // Jika hanya satu kategori dan itu job_status, tampilkan layer choropleth status pekerjaan
            if (activeCategories.length === 1 && activeCategories[0].type === 'job_status') {
                // Hapus layer lain dengan lebih robust
                if (window.jobStatusLayer) {
                    map.removeLayer(window.jobStatusLayer);
                    window.jobStatusLayer = null;
                }
                // Buat layer baru
                window.jobStatusLayer = createJobStatusLayer(window.currentFilteredData || alumniData, areaType);
                const geoJsonUrl = areaType === 'city' ? '/geojson/kota.geojson' : '/geojson/provinsi.geojson';
                $.getJSON(geoJsonUrl, function (geoData) {
                    // Tambahkan pengecekan ulang status checkbox 'Status Pekerjaan' sebelum menambah layer
                    if (!(categoryCheckboxRefs['job_status'] && categoryCheckboxRefs['job_status'].checked)) return;
                    // Pastikan layer belum ditambahkan
                    if (window.jobStatusLayer && !map.hasLayer(window.jobStatusLayer)) {
                        window.jobStatusLayer.addData(geoData);
                        map.addLayer(window.jobStatusLayer);
                        createAndUpdateLegend();
                    }
                });
                return;
            }

            // Jika hanya satu kategori dan itu salary, tampilkan layer choropleth penghasilan alumni
            if (activeCategories.length === 1 && activeCategories[0].type === 'salary') {
                console.log('Detected single salary category, creating salary layer');
                // Hapus layer lain dengan lebih robust
                if (window.salaryLayer) {
                    map.removeLayer(window.salaryLayer);
                    window.salaryLayer = null;
                }
                // Buat layer baru
                window.salaryLayer = createSalaryLayer(window.currentFilteredData || alumniData, areaType);
                const geoJsonUrl = areaType === 'city' ? '/geojson/kota.geojson' : '/geojson/provinsi.geojson';
                $.getJSON(geoJsonUrl, function (geoData) {
                    console.log('Salary layer: GeoJSON loaded, features:', geoData.features.length);
                    // Tambahkan pengecekan ulang status checkbox 'Penghasilan Alumni' sebelum menambah layer
                    if (!(categoryCheckboxRefs['salary'] && categoryCheckboxRefs['salary'].checked)) return;
                    // Pastikan layer belum ditambahkan
                    if (window.salaryLayer && !map.hasLayer(window.salaryLayer)) {
                        window.salaryLayer.addData(geoData);
                        map.addLayer(window.salaryLayer);
                        console.log('Salary layer added to map');
                        createAndUpdateLegend();
                    }
                });
                return;
            }

            // Jika hanya satu kategori dan itu job, tampilkan layer choropleth pekerjaan
            if (activeCategories.length === 1 && activeCategories[0].type === 'job') {
                console.log('Detected single job category, creating job layer');
                // Hapus layer lain dengan lebih robust
                if (window.jobLayer) {
                    map.removeLayer(window.jobLayer);
                    window.jobLayer = null;
                }
                // Buat layer baru
                window.jobLayer = createJobLayer(window.currentFilteredData || alumniData, areaType);
                const geoJsonUrl = areaType === 'city' ? '/geojson/kota.geojson' : '/geojson/provinsi.geojson';
                $.getJSON(geoJsonUrl, function (geoData) {
                    console.log('Job layer: GeoJSON loaded, features:', geoData.features.length);
                    // Tambahkan pengecekan ulang status checkbox 'Pekerjaan' sebelum menambah layer
                    if (!(categoryCheckboxRefs['job'] && categoryCheckboxRefs['job'].checked)) return;
                    // Pastikan layer belum ditambahkan
                    if (window.jobLayer && !map.hasLayer(window.jobLayer)) {
                        window.jobLayer.addData(geoData);
                        map.addLayer(window.jobLayer);
                        console.log('Job layer added to map');
                        createAndUpdateLegend();
                    }
                });
                return;
            }

            // Jika hanya satu kategori dan itu company, tampilkan layer choropleth jenis perusahaan
            if (activeCategories.length === 1 && activeCategories[0].type === 'company') {
                // Hapus layer lain dengan lebih robust
                if (window.companyTypeLayer) {
                    map.removeLayer(window.companyTypeLayer);
                    window.companyTypeLayer = null;
                }
                // Buat layer baru
                window.companyTypeLayer = createCompanyTypeLayer(window.currentFilteredData || alumniData, areaType);
                const geoJsonUrl = areaType === 'city' ? '/geojson/kota.geojson' : '/geojson/provinsi.geojson';
                $.getJSON(geoJsonUrl, function (geoData) {
                    // Tambahkan pengecekan ulang status checkbox 'Jenis Perusahaan' sebelum menambah layer
                    if (!(categoryCheckboxRefs['company'] && categoryCheckboxRefs['company'].checked)) return;
                    // Pastikan layer belum ditambahkan
                    if (window.companyTypeLayer && !map.hasLayer(window.companyTypeLayer)) {
                        window.companyTypeLayer.addData(geoData);
                        map.addLayer(window.companyTypeLayer);
                        createAndUpdateLegend();
                    }
                });
                return;
            }

            // Jika hanya satu kategori dan itu count, tampilkan layer choropleth jumlah alumni
            if (activeCategories.length === 1 && activeCategories[0].type === 'count') {
                // Hapus layer lain dengan lebih robust
                if (window.alumniCountLayer) {
                    map.removeLayer(window.alumniCountLayer);
                    window.alumniCountLayer = null;
                }
                // Buat layer baru
                window.alumniCountLayer = createAlumniCountLayer(window.currentFilteredData || alumniData, areaType);
                const geoJsonUrl = areaType === 'city' ? '/geojson/kota.geojson' : '/geojson/provinsi.geojson';
                $.getJSON(geoJsonUrl, function (geoData) {
                    // Tambahkan pengecekan ulang status checkbox 'Jumlah Alumni' sebelum menambah layer
                    if (!(categoryCheckboxRefs['count'] && categoryCheckboxRefs['count'].checked)) return;
                    // Pastikan layer belum ditambahkan
                    if (window.alumniCountLayer && !map.hasLayer(window.alumniCountLayer)) {
                        window.alumniCountLayer.addData(geoData);
                        map.addLayer(window.alumniCountLayer);
                        createAndUpdateLegend();
                    }
                });
                return;
            }

            // Ambil data alumni yang memenuhi semua filter kategori
            let filtered = (window.currentFilteredData || alumniData).slice();

            // Buat layer intersection
            const geoJsonUrl = areaType === 'city' ? '/geojson/kota.geojson' : '/geojson/provinsi.geojson';
            $.getJSON(geoJsonUrl, function (geoData) {
                window.intersectionLayer = L.geoJSON(geoData, {
                    style: function (feature) {
                        const locationName = areaType === 'province' ? feature.properties.NAME_1 : feature.properties.NAME_2;
                        const areaAlumni = filtered.filter(a =>
                            areaType === 'province' ? normalizeName(a.province) === normalizeName(locationName)
                                : normalizeName(a.city) === normalizeName(locationName)
                        );
                        const count = areaAlumni.length;
                        let color = getColor(count);

                        // Logic untuk menentukan warna berdasarkan kategori dominan
                        if (activeCategories.length === 1) {
                            if (activeCategories[0].type === 'company') {
                                const stats = getCompanyTypeStats(areaAlumni, locationName, areaType);
                                const companyDominant = getDominantCompanyType(stats);
                                color = getCompanyTypeColorByName(companyDominant, count);
                            } else if (activeCategories[0].type === 'salary') {
                                const salaryRanges = getSalaryRanges(areaAlumni, locationName, areaType);
                                const salaryDominant = getDominantSalaryRange(salaryRanges);
                                color = getDominantSalaryColor(salaryDominant, count);
                            } else if (activeCategories[0].type === 'job') {
                                const stats = getJobStats(areaAlumni, locationName, areaType);
                                const dominantJob = getDominantJob(stats);
                                color = getJobColorByName(dominantJob, count);
                            }
                        } else {
                            // Kombinasi: misal, company+salary, warna di-mix atau pilih salah satu (misal, company)
                            if (activeCategories.some(c => c.type === 'company')) {
                                const stats = getCompanyTypeStats(areaAlumni, locationName, areaType);
                                const companyDominant = getDominantCompanyType(stats);
                                color = getCompanyTypeColorByName(companyDominant, count);
                            } else if (activeCategories.some(c => c.type === 'salary')) {
                                const salaryRanges = getSalaryRanges(areaAlumni, locationName, areaType);
                                const salaryDominant = getDominantSalaryRange(salaryRanges);
                                color = getDominantSalaryColor(salaryDominant, count);
                            } else if (activeCategories.some(c => c.type === 'job_status')) {
                                const jobStatusStats = getJobStatusStats(areaAlumni, locationName, areaType);
                                const jobStatusDominant = getDominantJobStatus(jobStatusStats);
                                color = getJobStatusColorByName(jobStatusDominant, count);
                            } else if (activeCategories.some(c => c.type === 'job')) {
                                const stats = getJobStats(areaAlumni, locationName, areaType);
                                const dominantJob = getDominantJob(stats);
                                color = getJobColorByName(dominantJob, count);
                            } else {
                                color = getColor(count);
                            }
                        }
                        return {
                            fillColor: color,
                            weight: 1,
                            opacity: 1,
                            color: 'white',
                            fillOpacity: 0.7
                        };
                    },
                    onEachFeature: function (feature, layer) {
                        const locationName = areaType === 'province' ? feature.properties.NAME_1 : feature.properties.NAME_2;
                        let areaAlumni = filtered.filter(a =>
                            areaType === 'province' ? normalizeName(a.province) === normalizeName(locationName)
                                : normalizeName(a.city) === normalizeName(locationName)
                        );

                        // Helper function to create popup header
                        const createPopupHeader = () => {
                            return `<strong>${areaType === 'province' ? feature.properties.NAME_1 : feature.properties.NAME_2 + ', ' + feature.properties.NAME_1}</strong><br>`;
                        };

                        // Helper function to bind popup and events
                        const bindPopupAndEvents = (content) => {
                            layer.bindPopup(content);
                            layer.on({
                                mouseover: function (e) {
                                    var l = e.target;
                                    l.setStyle({
                                        weight: 3,
                                        color: '#666',
                                        dashArray: '',
                                        fillOpacity: 1
                                    });
                                },
                                mouseout: function (e) {
                                    layer.setStyle({
                                        weight: 1,
                                        color: 'white',
                                        fillOpacity: 0.7
                                    });
                                },
                                popupclose: function (e) {
                                    // Reset fillOpacity ke 0.7 saat popup ditutup
                                    layer.setStyle({
                                        fillOpacity: 0.7
                                    });
                                }
                            });
                        };

                        // Helper function to get salary range
                        const getSalaryRange = (salary) => {
                            if (salary >= 3000000 && salary <= 5000000) return '3 Juta - 5 Juta';
                            else if (salary > 5000000) return '> 5 Juta';
                            return '< 3 Juta';
                        };

                        // Helper function to get normalized company type
                        const getNormalizedCompanyType = (companyType) => {
                            let ctype = companyType;
                            if (typeof ctype === 'number' || (typeof ctype === 'string' && COMPANY_TYPE_LABELS[ctype])) {
                                ctype = COMPANY_TYPE_LABELS[ctype];
                            }
                            const companyLabels = Object.values(COMPANY_TYPE_LABELS);
                            return (!ctype || !companyLabels.includes(ctype)) ? 'Unknown' : ctype;
                        };

                        let popupContent = createPopupHeader();
                        const chartBlocks = [];
                        const tableBlocks = [];

                        // 1. SINGLE CATEGORY: Job only
                        if (activeCategories.length === 1 && activeCategories[0].type === 'job') {
                            const stats = getJobStats(areaAlumni, locationName, areaType);
                            const jobLabels = Object.keys(stats).filter(job => stats[job].jumlah > 0);
                            const jobPercents = jobLabels.map(job => stats[job].persentase);
                            const jobColors = jobLabels.map(job => getJobColorByName(job, areaAlumni.length));
                            const chartId = `jobChart_${locationName.replace(/\s+/g, '_')}`;
                            const chartHeight = Math.max(jobLabels.length * 32, 48);

                            chartBlocks.push(`<div style='width:100%;max-width:420px;margin:8px auto 16px auto;'>
                                <div style='font-weight:bold;font-size:15px;text-align:center;margin-bottom:4px;'>Distribusi Pekerjaan</div>
                                <canvas id='${chartId}' height='${chartHeight}' data-labels='${JSON.stringify(jobLabels)}' data-data='${JSON.stringify(jobPercents)}' data-colors='${JSON.stringify(jobColors)}'></canvas>
                            </div>`);

                            tableBlocks.push(`<div style='overflow-x:auto;'><table style='width:100%;max-width:100%;font-size:13px;text-align:left;border-collapse:collapse;'>
                                <tr style='background:#f5f6fa;font-weight:bold;font-size:14px;'>
                                    <th style='padding:6px 4px;'>Pekerjaan Saat Ini</th>
                                    <th style='padding:6px 4px;text-align:center;'>Persentase</th>
                                    <th style='padding:6px 4px;text-align:center;'>Jumlah Alumni</th>
                                </tr>` +
                                Object.entries(stats)
                                    .filter(([job, val]) => val.jumlah > 0)
                                    .sort((a, b) => b[1].jumlah - a[1].jumlah)
                                    .map(([job, val]) => `<tr style='border-bottom:1px solid #eee;'>
                                        <td style='padding:4px 4px;'>${job}</td>
                                        <td style='text-align:center;padding:4px 4px;'>${val.persentase.toFixed(2)}%</td>
                                        <td style='text-align:center;padding:4px 4px;'>${val.jumlah} orang</td>
                                    </tr>`).join('') +
                                `</table></div>`);

                            popupContent += chartBlocks.join('') + tableBlocks.join('');
                            bindPopupAndEvents(popupContent);
                            return;
                        }

                        // 2. TWO CATEGORIES: Job + Salary
                        if (activeCategories.length === 2 &&
                            ((activeCategories[0].type === 'job' && activeCategories[1].type === 'salary') ||
                                (activeCategories[0].type === 'salary' && activeCategories[1].type === 'job'))) {

                            const jobLabels = [...new Set(areaAlumni.map(a => a.job || 'Unknown'))];
                            const salaryLabels = ['< 3 Juta', '3 Juta - 5 Juta', '> 5 Juta'];
                            let crossData = jobLabels.map(() => []);

                            salaryLabels.forEach((slabel, sidx) => {
                                jobLabels.forEach((jlabel, jidx) => {
                                    const count = areaAlumni.filter(a => {
                                        const job = a.job || 'Unknown';
                                        const srange = getSalaryRange(a.salary || 0);
                                        return job === jlabel && srange === slabel;
                                    }).length;
                                    crossData[jidx][sidx] = count;
                                });
                            });

                            tableBlocks.push(`<div style='font-weight:bold;font-size:15px;text-align:center;margin-bottom:8px;'>Distribusi Pekerjaan & Penghasilan Alumni</div>`);
                            tableBlocks.push(`<div style='overflow-x:auto;'><table style='width:100%;max-width:100%;font-size:13px;text-align:left;border-collapse:collapse;'>
                                <tr style='background:#f5f6fa;font-weight:bold;font-size:14px;'>
                                    <th style='padding:6px 4px;'>Pekerjaan</th>` +
                                salaryLabels.map(x => `<th style='padding:6px 4px;text-align:center;'>${x}</th>`).join('') +
                                `</tr>` +
                                jobLabels.map((ylabel, idx) =>
                                    `<tr style='border-bottom:1px solid #eee;'>
                                        <td style='padding:4px 4px;'>${ylabel}</td>` +
                                    salaryLabels.map((xlabel, i) => `<td style='text-align:center;padding:4px 4px;'>${crossData[idx][i]}</td>`).join('') +
                                    `</tr>`
                                ).join('') +
                                `</table></div>`);

                            popupContent += chartBlocks.join('') + tableBlocks.join('');
                            bindPopupAndEvents(popupContent);
                            return;
                        }

                        // 3. TWO CATEGORIES: Job + Company
                        if (activeCategories.length === 2 &&
                            ((activeCategories[0].type === 'job' && activeCategories[1].type === 'company') ||
                                (activeCategories[0].type === 'company' && activeCategories[1].type === 'job'))) {

                            const jobLabels = [...new Set(areaAlumni.map(a => a.job || 'Unknown'))];
                            const companyLabels = Object.values(COMPANY_TYPE_LABELS);
                            let crossData = jobLabels.map(() => []);

                            companyLabels.forEach((clabel, cidx) => {
                                jobLabels.forEach((jlabel, jidx) => {
                                    const count = areaAlumni.filter(a => {
                                        const ctype = getNormalizedCompanyType(a.company_type);
                                        const job = a.job || 'Unknown';
                                        return ctype === clabel && job === jlabel;
                                    }).length;
                                    crossData[jidx][cidx] = count;
                                });
                            });

                            tableBlocks.push(`<div style='font-weight:bold;font-size:15px;text-align:center;margin-bottom:8px;'>Distribusi Pekerjaan & Jenis Perusahaan</div>`);
                            tableBlocks.push(`<div style='overflow-x:auto;'><table style='width:100%;max-width:100%;font-size:13px;text-align:left;border-collapse:collapse;'>
                                <tr style='background:#f5f6fa;font-weight:bold;font-size:14px;'>
                                    <th style='padding:6px 4px;'>Pekerjaan</th>` +
                                companyLabels.map(x => `<th style='padding:6px 4px;text-align:center;'>${x}</th>`).join('') +
                                `</tr>` +
                                jobLabels.map((ylabel, idx) =>
                                    `<tr style='border-bottom:1px solid #eee;'>
                                        <td style='padding:4px 4px;'>${ylabel}</td>` +
                                    companyLabels.map((xlabel, i) => `<td style='text-align:center;padding:4px 4px;'>${crossData[idx][i]}</td>`).join('') +
                                    `</tr>`
                                ).join('') +
                                `</table></div>`);

                            popupContent += chartBlocks.join('') + tableBlocks.join('');
                            bindPopupAndEvents(popupContent);
                            return;
                        }

                        // 4. THREE CATEGORIES: Job + Company + Salary
                        if (activeCategories.length === 3 &&
                            activeCategories.some(c => c.type === 'job') &&
                            activeCategories.some(c => c.type === 'company') &&
                            activeCategories.some(c => c.type === 'salary')) {

                            const jobLabels = [...new Set(areaAlumni.map(a => a.job || 'Unknown'))];
                            const companyLabels = Object.values(COMPANY_TYPE_LABELS);
                            const salaryLabels = ['< 3 Juta', '3 Juta - 5 Juta', '> 5 Juta'];

                            // Header tabel
                            let tableContent = `<div style='font-weight:bold;font-size:15px;text-align:center;margin-bottom:8px;'>Distribusi Pekerjaan, Jenis Perusahaan & Penghasilan Alumni</div>`;
                            tableContent += `<div style='overflow-x:auto;'>`;
                            tableContent += `<table style='width:100%;max-width:100%;font-size:11px;text-align:left;border-collapse:collapse;'>`;

                            // Header row 1: Pekerjaan dan Jenis Perusahaan
                            tableContent += `<tr style='background:#f5f6fa;font-weight:bold;font-size:12px;'>`;
                            tableContent += `<th rowspan='2' style='padding:6px 4px;border:1px solid #ddd;vertical-align:middle;'>Pekerjaan</th>`;
                            tableContent += `<th colspan='${companyLabels.length}' style='text-align:center;padding:6px 4px;border:1px solid #ddd;'>Jenis Perusahaan</th>`;
                            tableContent += `</tr>`;

                            // Header row 2: Company labels
                            tableContent += `<tr style='background:#f5f6fa;font-weight:bold;font-size:11px;'>`;
                            companyLabels.forEach(clabel => {
                                tableContent += `<th style='padding:4px 2px;text-align:center;border:1px solid #ddd;'>${clabel.length > 10 ? clabel.substring(0, 10) + '...' : clabel}</th>`;
                            });
                            tableContent += `</tr>`;

                            // Data rows
                            jobLabels.forEach(jlabel => {
                                tableContent += `<tr style='border-bottom:1px solid #eee;'>`;
                                tableContent += `<td style='padding:4px 4px;border:1px solid #ddd;font-weight:bold;'>${jlabel.length > 15 ? jlabel.substring(0, 15) + '...' : jlabel}</td>`;

                                companyLabels.forEach(clabel => {
                                    const salaryCounts = salaryLabels.map(slabel => {
                                        return areaAlumni.filter(a => {
                                            const ctype = getNormalizedCompanyType(a.company_type);
                                            const job = a.job || 'Unknown';
                                            const srange = getSalaryRange(a.salary || 0);
                                            return ctype === clabel && job === jlabel && srange === slabel;
                                        }).length;
                                    });

                                    const totalForCell = salaryCounts.reduce((sum, count) => sum + count, 0);
                                    const salaryBreakdown = salaryLabels.map((slabel, idx) => {
                                        if (salaryCounts[idx] > 0) {
                                            return `${slabel.replace(' Juta', 'J')}: ${salaryCounts[idx]}`;
                                        }
                                        return null;
                                    }).filter(item => item !== null).join('<br>');

                                    tableContent += `<td style='text-align:center;padding:3px 2px;border:1px solid #ddd;font-size:10px;'>`;
                                    if (totalForCell > 0) {
                                        tableContent += `<div style='font-weight:bold;color:#2c3e50;margin-bottom:2px;'>${totalForCell}</div>`;
                                        if (salaryBreakdown) {
                                            tableContent += `<div style='color:#666;'>${salaryBreakdown}</div>`;
                                        }
                                    } else {
                                        tableContent += `<span style='color:#ccc;'>-</span>`;
                                    }
                                    tableContent += `</td>`;
                                });
                                tableContent += `</tr>`;
                            });

                            tableContent += `</table>`;
                            tableContent += `<div style='margin-top:8px;font-size:10px;color:#666;text-align:center;'>`;
                            tableContent += `Format: Total per sel, lalu breakdown per range penghasilan`;
                            tableContent += `</div>`;
                            tableContent += `</div>`;

                            tableBlocks.push(tableContent);
                            popupContent += chartBlocks.join('') + tableBlocks.join('');
                            bindPopupAndEvents(popupContent);
                            return;
                        }

                        // Continue with existing categories...
                        popupContent += `Jumlah Alumni: <b>${areaAlumni.length}</b><br>`;

                        // Always show chart above table, depending on active categories
                        // 1. Jumlah Alumni (vertical bar chart)
                        if (activeCategories.length === 1 && activeCategories[0].type === 'count') {
                            const chartId = `alumniCountChart_${locationName.replace(/\s+/g, '_')}`;
                            chartBlocks.push(`<div style='width:100%;max-width:220px;margin:16px auto 8px auto;'>
                                <div style='font-weight:bold;font-size:15px;text-align:center;margin-bottom:4px;'>Jumlah Alumni</div>
                                <canvas id='${chartId}' height='180' data-labels='${JSON.stringify(["Jumlah Alumni"])}' data-data='${JSON.stringify([areaAlumni.length])}'></canvas>
                            </div>`);

                            if (areaType === 'province') {
                                const cityCounts = {};
                                areaAlumni.forEach(a => {
                                    const city = a.city || 'Tidak Diketahui';
                                    cityCounts[city] = (cityCounts[city] || 0) + 1;
                                });

                                let cityTable = `<div style='margin-top:10px; font-weight:bold; font-size:14px; text-align:center;'>Distribusi per Kota/Kabupaten</div>`;
                                cityTable += `<div style='overflow-y:auto; max-height: 150px; margin-top: 5px;'><table style='width:100%;font-size:13px;text-align:left;border-collapse:collapse;'>`;
                                cityTable += `<thead style='position:sticky;top:0;background:white;'><tr style='background:#f5f6fa;font-weight:bold;font-size:14px;'><th style='padding:6px 4px;'>Kota/Kab.</th><th style='padding:6px 4px;text-align:center;'>Jumlah</th></tr></thead>`;
                                cityTable += `<tbody>`;
                                Object.entries(cityCounts).sort((a, b) => b[1] - a[1]).forEach(([city, jumlah]) => {
                                    cityTable += `<tr style='border-bottom:1px solid #eee;'><td style='padding:4px 4px;'>${city}</td><td style='text-align:center;padding:4px 4px;'>${jumlah}</td></tr>`;
                                });
                                cityTable += `</tbody></table></div>`;
                                tableBlocks.push(cityTable);
                            } else {
                                tableBlocks.push(`<div style='overflow-x:auto;'><table style='width:100%;max-width:100%;font-size:13px;text-align:left;border-collapse:collapse;'><tr style='background:#f5f6fa;font-weight:bold;font-size:14px;'><th style='padding:6px 4px;'>Area</th><th style='padding:6px 4px;text-align:center;'>Jumlah</th></tr><tr><td style='padding:4px 4px;'>${locationName}</td><td style='text-align:center;padding:4px 4px;'>${areaAlumni.length}</td></tr></table></div>`);
                            }
                        }
                        // 2. Jenis Perusahaan (horizontal bar chart)
                        if (activeCategories.length === 1 && activeCategories[0].type === 'company') {
                            const stats = getCompanyTypeStats(areaAlumni, locationName, areaType);
                            const companyLabels = Object.values(COMPANY_TYPE_LABELS);
                            const companyPercents = companyLabels.map(label => stats[label] ? stats[label].persentase : 0);
                            const companyColors = companyLabels.map(label => COMPANY_TYPE_COLORS[label] || '#CCCCCC');
                            const chartId = `companyChart_${locationName.replace(/\s+/g, '_')}`;
                            chartBlocks.push(`<div style='width:100%;max-width:420px;margin:16px auto 8px auto;'>
                                <div style='font-weight:bold;font-size:15px;text-align:center;margin-bottom:4px;'>Jenis Perusahaan</div>
                                <canvas id='${chartId}' height='${companyLabels.length * 32}' data-labels='${JSON.stringify(companyLabels)}' data-data='${JSON.stringify(companyPercents)}' data-colors='${JSON.stringify(companyColors)}'></canvas>
                            </div>`);
                            tableBlocks.push(`<div style='overflow-x:auto;'><table style='width:100%;max-width:100%;font-size:13px;text-align:left;border-collapse:collapse;'><tr style='background:#f5f6fa;font-weight:bold;font-size:14px;'><th style='padding:6px 4px;'>Jenis</th><th style='padding:6px 4px;text-align:center;'>%</th><th style='padding:6px 4px;text-align:center;'>Jml</th></tr>` + Object.entries(stats).map(([type, val]) => {
                                let barColor = COMPANY_TYPE_COLORS[type] || '#CCCCCC';
                                return `<tr style='border-bottom:1px solid #eee;'><td style='padding:4px 4px;'>${type}</td><td style='text-align:center;padding:4px 4px;'>${val.persentase.toFixed(2)}%</td><td style='text-align:center;padding:4px 4px;'>${val.jumlah}</td></tr>`;
                            }).join('') + `</table></div>`);
                        }
                        // 3. Penghasilan (vertical bar chart)
                        if (activeCategories.length === 1 && activeCategories[0].type === 'salary') {
                            const salaryRanges = getSalaryRanges(areaAlumni, locationName, areaType);
                            const chartId = `salaryChart_${locationName.replace(/\s+/g, '_')}`;
                            chartBlocks.push(`<div style='width:100%;max-width:320px;margin:16px auto 8px auto;'>
                                <div style='font-weight:bold;font-size:15px;text-align:center;margin-bottom:4px;'>Penghasilan Alumni</div>
                                <canvas id='${chartId}' height='180' data-labels='${JSON.stringify(["< 3 Juta", "3 Juta - 5 Juta", "> 5 Juta"])}' data-data='${JSON.stringify([
                                salaryRanges["< 3 Juta"].persentase,
                                salaryRanges["3 Juta - 5 Juta"].persentase,
                                salaryRanges["> 5 Juta"].persentase
                            ])}'></canvas>
                            </div>`);
                            tableBlocks.push(`<div style='overflow-x:auto;'><table style='width:100%;max-width:100%;font-size:13px;text-align:left;border-collapse:collapse;'><tr style='background:#f5f6fa;font-weight:bold;font-size:14px;'><th style='padding:6px 4px;'>Range</th><th style='padding:6px 4px;text-align:center;'>%</th><th style='padding:6px 4px;text-align:center;'>Jml</th></tr>` + Object.entries(salaryRanges).map(([range, val]) => {
                                let barColor = getSalaryBarColor(range);
                                return `<tr style='border-bottom:1px solid #eee;'><td style='padding:4px 4px;'>${range}</td><td style='text-align:center;padding:4px 4px;'>${val.persentase.toFixed(2)}%</td><td style='text-align:center;padding:4px 4px;'>${val.jumlah}</td></tr>`;
                            }).join('') + `</table></div>`);
                        }
                        // 4. Gabungan company & salary (stacked horizontal bar chart)
                        if (activeCategories.some(c => c.type === 'company') && activeCategories.some(c => c.type === 'salary')) {
                            const companyLabels = Object.values(COMPANY_TYPE_LABELS);
                            const salaryLabels = ['< 3 Juta', '3 Juta - 5 Juta', '> 5 Juta'];
                            let salaryData = salaryLabels.map(() => []);
                            companyLabels.forEach(clabel => {
                                salaryLabels.forEach((slabel, i) => {
                                    const count = areaAlumni.filter(a => {
                                        let ctype = a.company_type;
                                        if (typeof ctype === 'number' || (typeof ctype === 'string' && COMPANY_TYPE_LABELS[ctype])) {
                                            ctype = COMPANY_TYPE_LABELS[ctype];
                                        }
                                        if (!ctype || !companyLabels.includes(ctype)) ctype = 'Unknown';
                                        let srange = '< 3 Juta';
                                        if (a.salary >= 3000000 && a.salary <= 5000000) srange = '3 Juta - 5 Juta';
                                        else if (a.salary > 5000000) srange = '> 5 Juta';
                                        else if (a.salary < 3000000) srange = '< 3 Juta';
                                        return ctype === clabel && srange === slabel;
                                    }).length;
                                    salaryData[i].push(count);
                                });
                            });
                            const companyTotals = companyLabels.map((clabel, idx) => salaryLabels.reduce((sum, _, i) => sum + salaryData[i][idx], 0));
                            let salaryPercents = salaryData.map((arr, i) => arr.map((v, idx) => companyTotals[idx] > 0 ? v / companyTotals[idx] * 100 : 0));
                            const chartId = `companySalaryChart_${locationName.replace(/\s+/g, '_')}`;
                            chartBlocks.push(`<div style='width:100%;max-width:480px;margin:16px auto 8px auto;'>
                                <div style='font-weight:bold;font-size:15px;text-align:center;margin-bottom:4px;'>Distribusi Jenis Perusahaan & Penghasilan</div>
                                <canvas id='${chartId}' height='${companyLabels.length * 32}' data-labels='${JSON.stringify(companyLabels)}' data-salary-labels='${JSON.stringify(salaryLabels)}' data-data='${JSON.stringify(salaryPercents)}'></canvas>
                            </div>`);
                            // Tabel cross-tab tetap di bawah chart
                            tableBlocks.push(`<div style='overflow-x:auto;'><table style='width:100%;max-width:100%;font-size:13px;text-align:left;border-collapse:collapse;'><tr style='background:#f5f6fa;font-weight:bold;font-size:14px;'><th style='padding:6px 4px;'>Jenis Perusahaan</th>` + salaryLabels.map(s => `<th style='padding:6px 4px;text-align:center;'>${s}</th>`).join('') + `<th style='padding:6px 4px;text-align:center;'>Total</th><th style='padding:6px 4px;text-align:center;'>%</th></tr>` + companyLabels.map((clabel, idx) => {
                                let rowTotal = salaryLabels.reduce((sum, _, i) => sum + salaryData[i][idx], 0);
                                let percent = companyTotals[idx] > 0 ? (companyTotals[idx] / companyTotals.reduce((a, b) => a + b, 0)) * 100 : 0;
                                let barColor = COMPANY_TYPE_COLORS[clabel] || '#CCCCCC';
                                return `<tr style='border-bottom:1px solid #eee;'><td style='padding:4px 4px;'>${clabel}</td>` + salaryLabels.map((slabel, i) => `<td style='text-align:center;padding:4px 4px;'>${salaryData[i][idx]}</td>`).join('') + `<td style='text-align:center;font-weight:bold;padding:4px 4px;'>${rowTotal}</td><td style='min-width:70px;padding:4px 4px;'><div style='background:#eee;width:100%;height:14px;border-radius:7px;overflow:hidden;position:relative;'><div style='width:${percent}%;background:${barColor};height:100%;border-radius:7px;transition:width 0.5s;position:relative;'>${percent > 15 ? `<span style='font-size:11px;color:#222;position:absolute;left:8px;top:0;line-height:14px;'>${percent.toFixed(2)}%</span>` : `</div><span style='font-size:11px;color:#222;position:absolute;left:8px;top:0;line-height:14px;'>${percent.toFixed(2)}%</span><div style='display:none;'>`}</div></div></td></tr>`;
                            }).join('') + `</table></div>`);
                        }
                        // 5. Status Pekerjaan (horizontal bar chart)
                        if (activeCategories.length === 1 && activeCategories[0].type === 'job_status') {
                            const statusLabels = ['Bekerja', 'Wirausaha', 'Studi Lanjut', 'Mencari Kerja', 'Belum memungkinkan bekerja'];
                            const statusCounts = {};
                            statusLabels.forEach(label => statusCounts[label] = 0);
                            areaAlumni.forEach(a => {
                                if (statusLabels.includes(a.status)) statusCounts[a.status]++;
                                else statusCounts['Belum memungkinkan bekerja']++;
                            });
                            const total = areaAlumni.length;
                            const percents = statusLabels.map(label => total > 0 ? (statusCounts[label] / total * 100) : 0);
                            const chartId = `jobStatusChart_${locationName.replace(/\s+/g, '_')}`;
                            chartBlocks.push(`<div style='width:100%;max-width:420px;margin:16px auto 8px auto;'>
                                <div style='font-weight:bold;font-size:15px;text-align:center;margin-bottom:4px;'>Status Pekerjaan</div>
                                <canvas id='${chartId}' height='${statusLabels.length * 32}' data-labels='${JSON.stringify(statusLabels)}' data-data='${JSON.stringify(percents)}'></canvas>
                            </div>`);
                            tableBlocks.push(`<div style='overflow-x:auto;'><table style='width:100%;max-width:100%;font-size:13px;text-align:left;border-collapse:collapse;'><tr style='background:#f5f6fa;font-weight:bold;font-size:14px;'><th style='padding:6px 4px;'>Status</th><th style='padding:6px 4px;text-align:center;'>%</th><th style='padding:6px 4px;text-align:center;'>Jml</th></tr>` + statusLabels.map(label => {
                                return `<tr style='border-bottom:1px solid #eee;'><td style='padding:4px 4px;'>${label}</td><td style='text-align:center;padding:4px 4px;'>${percents[statusLabels.indexOf(label)].toFixed(2)}%</td><td style='text-align:center;padding:4px 4px;'>${statusCounts[label]}</td></tr>`;
                            }).join('') + `</table></div>`);
                        }
                        // 5. TWO CATEGORIES: Job Status + Job
                        if (activeCategories.length === 2 &&
                            ((activeCategories[0].type === 'job_status' && activeCategories[1].type === 'job') ||
                                (activeCategories[0].type === 'job' && activeCategories[1].type === 'job_status'))) {

                            const jobLabels = [...new Set(areaAlumni.map(a => a.job || 'Unknown'))];
                            const statusLabels = ['Bekerja', 'Wirausaha', 'Studi Lanjut', 'Mencari Kerja', 'Belum memungkinkan bekerja'];
                            const JOB_STATUS_MAP = {
                                1: 'Bekerja', 2: 'Wirausaha', 3: 'Studi Lanjut', 4: 'Mencari Kerja', 5: 'Belum memungkinkan bekerja'
                            };
                            let crossData = jobLabels.map(() => []);

                            statusLabels.forEach((slabel, sidx) => {
                                jobLabels.forEach((jlabel, jidx) => {
                                    const count = areaAlumni.filter(a => {
                                        const job = a.job || 'Unknown';
                                        let status = a.status;
                                        if (typeof status === 'number' || (typeof status === 'string' && JOB_STATUS_MAP[status])) {
                                            status = JOB_STATUS_MAP[status];
                                        }
                                        if (!statusLabels.includes(status)) status = 'Belum memungkinkan bekerja';
                                        return job === jlabel && status === slabel;
                                    }).length;
                                    crossData[jidx][sidx] = count;
                                });
                            });

                            tableBlocks.push(`<div style='font-weight:bold;font-size:15px;text-align:center;margin-bottom:8px;'>Distribusi Pekerjaan & Status Pekerjaan</div>`);
                            tableBlocks.push(`<div style='overflow-x:auto;'><table style='width:100%;max-width:100%;font-size:13px;text-align:left;border-collapse:collapse;'>
                                <tr style='background:#f5f6fa;font-weight:bold;font-size:14px;'>
                                    <th style='padding:6px 4px;'>Pekerjaan</th>` +
                                statusLabels.map(x => `<th style='padding:6px 4px;text-align:center;'>${x}</th>`).join('') +
                                `</tr>` +
                                jobLabels.map((ylabel, idx) =>
                                    `<tr style='border-bottom:1px solid #eee;'>
                                        <td style='padding:4px 4px;'>${ylabel}</td>` +
                                    statusLabels.map((xlabel, i) => `<td style='text-align:center;padding:4px 4px;'>${crossData[idx][i]}</td>`).join('') +
                                    `</tr>`
                                ).join('') +
                                `</table></div>`);

                            popupContent += chartBlocks.join('') + tableBlocks.join('');
                            bindPopupAndEvents(popupContent);
                            return;
                        }

                        // 6. THREE CATEGORIES: Job Status + Company + Salary  
                        if (activeCategories.length === 3 &&
                            activeCategories.some(c => c.type === 'job_status') &&
                            activeCategories.some(c => c.type === 'company') &&
                            activeCategories.some(c => c.type === 'salary')) {

                            const statusLabels = ['Bekerja', 'Wirausaha', 'Studi Lanjut', 'Mencari Kerja', 'Belum memungkinkan bekerja'];
                            const JOB_STATUS_MAP = {
                                1: 'Bekerja', 2: 'Wirausaha', 3: 'Studi Lanjut', 4: 'Mencari Kerja', 5: 'Belum memungkinkan bekerja'
                            };
                            const companyLabels = Object.values(COMPANY_TYPE_LABELS);
                            const salaryLabels = ['< 3 Juta', '3 Juta - 5 Juta', '> 5 Juta'];

                            let tableContent = `<div style='font-weight:bold;font-size:15px;text-align:center;margin-bottom:8px;'>Distribusi Status Pekerjaan, Jenis Perusahaan & Penghasilan Alumni</div>`;
                            tableContent += `<div style='overflow-x:auto;'>`;
                            tableContent += `<table style='width:100%;max-width:100%;font-size:11px;text-align:left;border-collapse:collapse;'>`;

                            // Header
                            tableContent += `<tr style='background:#f5f6fa;font-weight:bold;font-size:12px;'>`;
                            tableContent += `<th rowspan='2' style='padding:6px 4px;border:1px solid #ddd;vertical-align:middle;'>Status</th>`;
                            tableContent += `<th colspan='${companyLabels.length}' style='text-align:center;padding:6px 4px;border:1px solid #ddd;'>Jenis Perusahaan</th>`;
                            tableContent += `</tr>`;

                            tableContent += `<tr style='background:#f5f6fa;font-weight:bold;font-size:11px;'>`;
                            companyLabels.forEach(clabel => {
                                tableContent += `<th style='padding:4px 2px;text-align:center;border:1px solid #ddd;'>${clabel.length > 10 ? clabel.substring(0, 10) + '...' : clabel}</th>`;
                            });
                            tableContent += `</tr>`;

                            // Data rows
                            statusLabels.forEach(slabel => {
                                tableContent += `<tr style='border-bottom:1px solid #eee;'>`;
                                tableContent += `<td style='padding:4px 4px;border:1px solid #ddd;font-weight:bold;'>${slabel}</td>`;

                                companyLabels.forEach(clabel => {
                                    const salaryCounts = salaryLabels.map(salRange => {
                                        return areaAlumni.filter(a => {
                                            const ctype = getNormalizedCompanyType(a.company_type);
                                            let status = a.status;
                                            if (typeof status === 'number' || (typeof status === 'string' && JOB_STATUS_MAP[status])) {
                                                status = JOB_STATUS_MAP[status];
                                            }
                                            if (!statusLabels.includes(status)) status = 'Belum memungkinkan bekerja';
                                            const srange = getSalaryRange(a.salary || 0);
                                            return ctype === clabel && status === slabel && srange === salRange;
                                        }).length;
                                    });

                                    const totalForCell = salaryCounts.reduce((sum, count) => sum + count, 0);
                                    const salaryBreakdown = salaryLabels.map((salRange, idx) => {
                                        if (salaryCounts[idx] > 0) {
                                            return `${salRange.replace(' Juta', 'J')}: ${salaryCounts[idx]}`;
                                        }
                                        return null;
                                    }).filter(item => item !== null).join('<br>');

                                    tableContent += `<td style='text-align:center;padding:3px 2px;border:1px solid #ddd;font-size:10px;'>`;
                                    if (totalForCell > 0) {
                                        tableContent += `<div style='font-weight:bold;color:#2c3e50;margin-bottom:2px;'>${totalForCell}</div>`;
                                        if (salaryBreakdown) {
                                            tableContent += `<div style='color:#666;'>${salaryBreakdown}</div>`;
                                        }
                                    } else {
                                        tableContent += `<span style='color:#ccc;'>-</span>`;
                                    }
                                    tableContent += `</td>`;
                                });
                                tableContent += `</tr>`;
                            });

                            tableContent += `</table>`;
                            tableContent += `<div style='margin-top:8px;font-size:10px;color:#666;text-align:center;'>`;
                            tableContent += `Format: Total per sel, lalu breakdown per range penghasilan`;
                            tableContent += `</div>`;
                            tableContent += `</div>`;

                            tableBlocks.push(tableContent);
                            popupContent += chartBlocks.join('') + tableBlocks.join('');
                            bindPopupAndEvents(popupContent);
                            return;
                        }

                        // Gabungkan chart dan tabel untuk kategori lainnya
                        popupContent += chartBlocks.join('') + tableBlocks.join('');
                        bindPopupAndEvents(popupContent);
                    }
                });
                map.addLayer(window.intersectionLayer);
                // Legend gabungan
                createAndUpdateLegend();
            });
        }

        return container;
    }
});
L.control.layerControl = function (opts) { return new L.Control.LayerControl(opts); };

// Custom Marker Toggle
L.Control.MarkerToggle = L.Control.extend({
    options: { position: 'topright' },
    onAdd: function (map) {
        const container = L.DomUtil.create('div', 'leaflet-bar leaflet-control');
        container.style.backgroundColor = 'rgba(255, 255, 255, 0.95)';
        container.style.padding = '10px';
        container.style.borderRadius = '6px';
        container.style.boxShadow = '0 2px 8px rgba(0,0,0,0.25)';
        container.style.display = 'flex';
        container.style.alignItems = 'center';
        container.style.marginBottom = '10px';
        container.style.border = '1px solid #e0e0e0';
        container.style.zIndex = '999';
        container.style.position = 'relative';

        const markerIcon = L.DomUtil.create('img', '', container);
        markerIcon.src = '/images/marker.png';
        markerIcon.style.width = '20px';
        markerIcon.style.height = '20px';
        markerIcon.style.marginRight = '5px';
        const checkbox = L.DomUtil.create('input', '', container);
        checkbox.type = 'checkbox';
        checkbox.id = 'markerToggle';
        checkbox.checked = true;
        checkbox.style.margin = '0 5px';
        const label = L.DomUtil.create('label', '', container);
        label.htmlFor = 'markerToggle';
        label.innerHTML = 'Tampilkan Marker';
        label.style.marginLeft = '2px';
        label.style.fontSize = '12px';
        L.DomEvent.disableClickPropagation(container);
        L.DomEvent.disableScrollPropagation(container);
        L.DomEvent.on(checkbox, 'change', function () {
            if (window.alumniMarkersCluster) {
                if (this.checked) map.addLayer(window.alumniMarkersCluster);
                else map.removeLayer(window.alumniMarkersCluster);
            }
        });
        return container;
    }
});
L.control.markerToggle = function (opts) { return new L.Control.MarkerToggle(opts); };

// Custom Legend
L.Control.Legend = L.Control.extend({
    options: {
        position: 'bottomright'
    },

    onAdd: function (map) {
        const div = L.DomUtil.create('div', 'legend-control');
        div.style.cssText = `
            background: rgba(255, 255, 255, 0.95); 
            border-radius: 8px; 
            box-shadow: 0 2px 10px rgba(0,0,0,0.2); 
            max-width: 280px; 
            font-family: Arial, sans-serif; 
            font-size: 12px; 
            border: 1px solid #ccc;
            z-index: 998;
        `;

        // Header dengan tombol toggle
        const header = L.DomUtil.create('div', '', div);
        header.style.cssText = `
            background: #f8f9fa;
            padding: 8px 12px;
            border-bottom: 1px solid #dee2e6;
            border-radius: 8px 8px 0 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-weight: bold;
            color: #495057;
            cursor: pointer;
        `;

        const headerTitle = L.DomUtil.create('span', '', header);
        headerTitle.innerHTML = '📊 Legend';

        const toggleButton = L.DomUtil.create('button', '', header);
        toggleButton.innerHTML = '−'; // minimize symbol
        toggleButton.style.cssText = `
            background: none;
            border: none;
            font-size: 16px;
            font-weight: bold;
            color: #6c757d;
            cursor: pointer;
            padding: 0;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
        `;

        // Content container
        const contentDiv = L.DomUtil.create('div', '', div);
        contentDiv.style.cssText = `
            padding: 10px 12px;
            max-height: 300px;
            overflow-y: auto;
            overflow-x: hidden;
        `;

        // State untuk toggle
        let isCollapsed = false;

        // Toggle functionality
        const toggleLegend = () => {
            isCollapsed = !isCollapsed;
            if (isCollapsed) {
                contentDiv.style.display = 'none';
                toggleButton.innerHTML = '+';
                headerTitle.innerHTML = '📊 Legend (tersembunyi)';
                div.style.width = 'auto';
            } else {
                contentDiv.style.display = 'block';
                toggleButton.innerHTML = '−';
                headerTitle.innerHTML = '📊 Legend';
            }
        };

        // Event listeners
        L.DomEvent.on(header, 'click', toggleLegend);
        L.DomEvent.on(toggleButton, 'click', function (e) {
            L.DomEvent.stopPropagation(e);
            toggleLegend();
        });

        // Prevent map interaction
        L.DomEvent.disableClickPropagation(div);
        L.DomEvent.disableScrollPropagation(div);

        // Bind update method ke instance control
        const self = this;
        self.update = function () {
            const activeTypes = [];
            const categoryCheckboxes = document.querySelectorAll('.category-checkbox:checked');
            console.log('Found checked checkboxes:', categoryCheckboxes.length);
            categoryCheckboxes.forEach((checkbox, index) => {
                console.log(`Checkbox ${index}: value="${checkbox.value}", id="${checkbox.id}"`);
                activeTypes.push(checkbox.value);
            });

            if (activeTypes.length === 0) {
                contentDiv.innerHTML = '<div style="color:#666;font-style:italic;text-align:center;">Pilih kategori untuk legend</div>';
                return;
            }

            console.log('Creating legend for active types:', activeTypes);
            console.log('First active type:', activeTypes[0]);

            let legendContent = '';

            // Header dengan kombinasi aktif
            if (activeTypes.length > 1) {
                const categories = [
                    { name: 'Jumlah Alumni', type: 'count' },
                    { name: 'Jenis Perusahaan', type: 'company' },
                    { name: 'Penghasilan Alumni', type: 'salary' },
                    { name: 'Status Pekerjaan', type: 'job_status' },
                    { name: 'Pekerjaan', type: 'job' }
                ];
                const combinationNames = activeTypes.map(type =>
                    categories.find(cat => cat.type === type)?.name || type
                ).join(' + ');
                legendContent += `<div style="font-weight:bold;color:#2c3e50;margin-bottom:8px;border-bottom:1px solid #eee;padding-bottom:4px;">📊 ${combinationNames}</div>`;
                console.log('Added combination header, content length:', legendContent.length);
            }

            // Legend untuk single kategori - gunakan warna dari fungsi yang sama dengan layer
            if (activeTypes.length === 1) {
                const type = activeTypes[0];
                console.log('Processing single category:', type);

                // Cek semua kemungkinan nilai
                if (type === 'count' || type === 'alumniCountLayer') {
                    console.log('Processing count legend');
                    legendContent += '<div style="margin-bottom:6px;"><strong style="color:#2c3e50;">Jumlah Alumni</strong></div>';
                    legendContent += '<div style="display:grid;grid-template-columns:repeat(2,1fr);gap:3px;margin-bottom:8px;">';

                    // Gunakan fungsi getColor yang sama dengan layer
                    const ranges = [
                        { label: '1 alumni', count: 1 },
                        { label: '2-3 alumni', count: 3 },
                        { label: '4-5 alumni', count: 5 },
                        { label: '6-10 alumni', count: 8 },
                        { label: '11-15 alumni', count: 13 },
                        { label: '16+ alumni', count: 20 }
                    ];

                    ranges.forEach(item => {
                        let color = '#CCCCCC'; // fallback
                        try {
                            color = getColor(item.count);
                            console.log('Count color for', item.count, ':', color);
                        } catch (e) {
                            console.log('Error getting color for count:', e);
                        }
                        legendContent += `<div style="display:flex;align-items:center;margin:2px 0;font-size:10px;">
                            <i style="background:${color};width:12px;height:12px;margin-right:4px;opacity:0.8;border:1px solid #ddd;border-radius:2px;"></i>
                            ${item.label}
                        </div>`;
                    });
                    legendContent += '</div>';
                    console.log('Count legend content length:', legendContent.length);
                }

                else if (type === 'salary' || type === 'salaryLayer') {
                    console.log('Processing salary legend');
                    legendContent += '<div style="margin-bottom:6px;"><strong style="color:#2c3e50;">Penghasilan Alumni</strong></div>';
                    legendContent += '<div style="display:grid;grid-template-columns:repeat(1,1fr);gap:3px;margin-bottom:8px;">';

                    // Gunakan warna yang sama dengan getDominantSalaryColor
                    const salaryRanges = [
                        { label: '< 3 Juta', range: '< 3 Juta' },
                        { label: '3 Juta - 5 Juta', range: '3 Juta - 5 Juta' },
                        { label: '> 5 Juta', range: '> 5 Juta' }
                    ];

                    salaryRanges.forEach(item => {
                        let color = '#CCCCCC'; // fallback
                        try {
                            if (typeof getDominantSalaryColor === 'function') {
                                color = getDominantSalaryColor(item.range, 10);
                                console.log('Salary color for', item.range, ':', color);
                            } else {
                                // Fallback colors
                                const fallbackColors = {
                                    '< 3 Juta': '#ffffcc',
                                    '3 Juta - 5 Juta': '#a1dab4',
                                    '> 5 Juta': '#41b6c4'
                                };
                                color = fallbackColors[item.range] || '#CCCCCC';
                                console.log('Using fallback salary color for', item.range, ':', color);
                            }
                        } catch (e) {
                            console.log('Error getting salary color:', e);
                            // Hard-coded fallback
                            const fallbackColors = {
                                '< 3 Juta': '#ffffcc',
                                '3 Juta - 5 Juta': '#a1dab4',
                                '> 5 Juta': '#41b6c4'
                            };
                            color = fallbackColors[item.range] || '#CCCCCC';
                        }
                        legendContent += `<div style="display:flex;align-items:center;margin:2px 0;font-size:11px;">
                            <i style="background:${color};width:12px;height:12px;margin-right:6px;opacity:0.8;border-radius:2px;border:1px solid #ddd;"></i>
                            ${item.label}
                        </div>`;
                    });
                    legendContent += '</div>';
                    console.log('Salary legend content length:', legendContent.length);
                }

                else if (type === 'company' || type === 'companyTypeLayer') {
                    console.log('Processing company legend');
                    legendContent += '<div style="margin-bottom:6px;"><strong style="color:#2c3e50;">Jenis Perusahaan</strong></div>';
                    legendContent += '<div style="display:grid;grid-template-columns:repeat(1,1fr);gap:3px;margin-bottom:8px;">';

                    console.log('COMPANY_TYPE_COLORS available:', typeof COMPANY_TYPE_COLORS, COMPANY_TYPE_COLORS);

                    if (typeof COMPANY_TYPE_COLORS !== 'undefined' && COMPANY_TYPE_COLORS) {
                        Object.entries(COMPANY_TYPE_COLORS).forEach(([label, color]) => {
                            if (label !== 'Unknown' && label !== 'Lainnya') {
                                console.log('Company color for', label, ':', color);
                                legendContent += `<div style="display:flex;align-items:center;margin:2px 0;font-size:11px;">
                                    <i style="background:${color};width:12px;height:12px;margin-right:6px;opacity:0.8;border-radius:2px;border:1px solid #ddd;"></i>
                                    ${label.length > 18 ? label.substring(0, 18) + '...' : label}
                                </div>`;
                            }
                        });
                    } else {
                        // Fallback colors jika COMPANY_TYPE_COLORS tidak tersedia
                        const fallbackCompanies = [
                            { label: 'Instansi pemerintah', color: '#2E86C1' },
                            { label: 'BUMN/BUMD', color: '#F39C12' },
                            { label: 'Perusahaan swasta', color: '#8E44AD' },
                            { label: 'Wiraswasta', color: '#27AE60' }
                        ];
                        fallbackCompanies.forEach(item => {
                            legendContent += `<div style="display:flex;align-items:center;margin:2px 0;font-size:11px;">
                                <i style="background:${item.color};width:12px;height:12px;margin-right:6px;opacity:0.8;border-radius:2px;border:1px solid #ddd;"></i>
                                ${item.label}
                            </div>`;
                        });
                    }
                    legendContent += '</div>';
                    console.log('Company legend content length:', legendContent.length);
                }

                else if (type === 'job_status' || type === 'jobStatusLayer') {
                    console.log('Processing job_status legend');
                    legendContent += '<div style="margin-bottom:6px;"><strong style="color:#2c3e50;">Status Pekerjaan</strong></div>';
                    legendContent += '<div style="display:grid;grid-template-columns:repeat(1,1fr);gap:3px;margin-bottom:8px;">';

                    const jobStatusList = ['Bekerja', 'Wirausaha', 'Studi Lanjut', 'Mencari Kerja', 'Belum memungkinkan bekerja'];
                    jobStatusList.forEach(status => {
                        let color = '#CCCCCC'; // fallback
                        try {
                            if (typeof getJobStatusColorByName === 'function') {
                                color = getJobStatusColorByName(status, 10);
                                console.log('Job status color for', status, ':', color);
                            } else {
                                // Fallback colors
                                const fallbackColors = {
                                    'Bekerja': '#2E8B57',
                                    'Wirausaha': '#DC143C',
                                    'Studi Lanjut': '#4169E1',
                                    'Mencari Kerja': '#8E44AD',
                                    'Belum memungkinkan bekerja': '#808080'
                                };
                                color = fallbackColors[status] || '#CCCCCC';
                                console.log('Using fallback job status color for', status, ':', color);
                            }
                        } catch (e) {
                            console.log('Error getting job status color:', e);
                            const fallbackColors = {
                                'Bekerja': '#2E8B57',
                                'Wirausaha': '#DC143C',
                                'Studi Lanjut': '#4169E1',
                                'Mencari Kerja': '#808080',
                                'Belum memungkinkan bekerja': '#808080'
                            };
                            color = fallbackColors[status] || '#CCCCCC';
                        }
                        legendContent += `<div style="display:flex;align-items:center;margin:2px 0;font-size:11px;">
                            <i style="background:${color};width:12px;height:12px;margin-right:6px;opacity:0.8;border-radius:2px;border:1px solid #ddd;"></i>
                            ${status}
                        </div>`;
                    });
                    legendContent += '</div>';
                    console.log('Job status legend content length:', legendContent.length);
                }

                else if (type === 'job' || type === 'jobLayer') {
                    console.log('Processing job legend');
                    legendContent += '<div style="margin-bottom:6px;"><strong style="color:#2c3e50;">Pekerjaan Saat Ini</strong></div>';
                    legendContent += '<div style="font-size:10px;color:#666;margin-bottom:4px;">Warna berdasarkan jenis pekerjaan dominan</div>';
                    legendContent += '<div style="display:grid;grid-template-columns:repeat(1,1fr);gap:3px;margin-bottom:8px;">';

                    // Ambil data pekerjaan real dari alumni data
                    let realJobs = [];
                    try {
                        if (typeof getJobLabelsFromData === 'function' && (window.currentFilteredData || window.alumniData)) {
                            realJobs = getJobLabelsFromData(window.currentFilteredData || window.alumniData);
                            console.log('Real jobs from data:', realJobs);
                        } else {
                            // Fallback ke data umum berdasarkan screenshot
                            realJobs = [
                                'IT Support', 'Frontend Developer', 'Backend Developer', 'Fullstack Developer',
                                'UI/UX Designer', 'Data Analyst', 'Network Administrator', 'Network Engineer',
                                'PNS', 'Karyawan Swasta', 'Wiraswasta', 'Guru/Dosen', 'Konsultan'
                            ];
                        }
                    } catch (e) {
                        console.log('Error getting real job data:', e);
                        // Fallback berdasarkan yang terlihat di popup
                        realJobs = [
                            'IT Support', 'Frontend Developer', 'Backend Developer', 'Fullstack Developer',
                            'UI/UX Designer', 'Data Analyst', 'Network Administrator', 'Network Engineer',
                            'Tidak Diketahui'
                        ];
                    }

                    // Batasi ke 10 pekerjaan teratas untuk legend
                    const topJobs = realJobs.slice(0, 10);

                    topJobs.forEach((job, index) => {
                        let color = '#CCCCCC'; // fallback
                        try {
                            if (typeof getJobColorByName === 'function') {
                                color = getJobColorByName(job, 10);
                                console.log('Job color for', job, ':', color);
                            } else {
                                // Enhanced fallback colors - lebih bervariasi
                                const enhancedColors = [
                                    '#FF6B6B', '#4ECDC4', '#45B7D1', '#96CEB4', '#FECA57',  // Baris 1
                                    '#FF9FF3', '#54A0FF', '#5F27CD', '#00D2D3', '#FF9F43',  // Baris 2
                                    '#FF6348', '#2ED573', '#3742FA', '#A55EEA', '#26C6DA',  // Baris 3
                                    '#FFA502', '#FF3838', '#1B9CFC', '#55A3FF', '#FD79A8'   // Baris 4
                                ];

                                // Hash-based untuk konsistensi warna
                                let hash = 0;
                                for (let i = 0; i < job.length; i++) {
                                    hash = job.charCodeAt(i) + ((hash << 5) - hash);
                                }
                                color = enhancedColors[Math.abs(hash) % enhancedColors.length];
                                console.log('Using enhanced fallback job color for', job, ':', color);
                            }
                        } catch (e) {
                            console.log('Error getting job color:', e);
                            // Simple colorful fallback
                            const simpleColors = [
                                '#FF6B6B', '#4ECDC4', '#45B7D1', '#96CEB4', '#FECA57',
                                '#FF9FF3', '#54A0FF', '#5F27CD', '#00D2D3', '#FF9F43'
                            ];
                            color = simpleColors[index % simpleColors.length];
                        }

                        // Potong nama pekerjaan yang terlalu panjang
                        const displayName = job.length > 20 ? job.substring(0, 20) + '...' : job;

                        legendContent += `<div style="display:flex;align-items:center;margin:2px 0;font-size:11px;">
                            <i style="background:${color};width:12px;height:12px;margin-right:6px;opacity:0.8;border-radius:2px;border:1px solid #ddd;"></i>
                            ${displayName}
                        </div>`;
                    });

                    // Tambahkan info jika ada lebih banyak pekerjaan
                    if (realJobs.length > 10) {
                        legendContent += `<div style="font-size:10px;color:#666;margin-top:4px;font-style:italic;">...dan ${realJobs.length - 10} pekerjaan lainnya</div>`;
                    }

                    legendContent += '</div>';
                    console.log('Job legend content length:', legendContent.length);
                }

                // Catch-all untuk nilai yang tidak dikenali
                else {
                    console.log('Unrecognized category type:', type);
                    legendContent += `<div style="margin-bottom:6px;"><strong style="color:#2c3e50;">Kategori: ${type}</strong></div>`;
                    legendContent += `<div style="color:#666;font-style:italic;">Debug: Nilai checkbox tidak dikenali</div>`;
                }

                console.log('Final single category content length:', legendContent.length);
            }

            // Legend untuk kombinasi kategori
            else if (activeTypes.length > 1) {
                legendContent += '<div style="margin-bottom:6px;"><strong style="color:#2c3e50;">Warna Kombinasi</strong></div>';
                legendContent += '<div style="font-size:10px;color:#666;margin-bottom:6px;">Warna berdasarkan kategori dominan di setiap area</div>';

                // Tampilkan semua kategori yang terlibat
                if (activeTypes.includes('company')) {
                    legendContent += '<div style="margin-bottom:4px;"><strong style="font-size:11px;">Jenis Perusahaan:</strong></div>';
                    legendContent += '<div style="display:grid;grid-template-columns:repeat(2,1fr);gap:2px;margin-bottom:6px;margin-left:8px;">';
                    if (typeof COMPANY_TYPE_COLORS !== 'undefined') {
                        Object.entries(COMPANY_TYPE_COLORS).forEach(([label, color]) => {
                            if (label !== 'Unknown' && label !== 'Lainnya') {
                                legendContent += `<div style="display:flex;align-items:center;margin:1px 0;font-size:10px;">
                                    <i style="background:${color};width:8px;height:8px;margin-right:3px;opacity:0.8;border-radius:1px;"></i>
                                    ${label.length > 10 ? label.substring(0, 10) + '...' : label}
                                </div>`;
                            }
                        });
                    }
                    legendContent += '</div>';
                }

                if (activeTypes.includes('salary')) {
                    legendContent += '<div style="margin-bottom:4px;"><strong style="font-size:11px;">Penghasilan:</strong></div>';
                    legendContent += '<div style="margin-bottom:6px;margin-left:8px;">';
                    const salaryRanges = [
                        { label: '< 3 Juta', range: '< 3 Juta' },
                        { label: '3-5 Juta', range: '3 Juta - 5 Juta' },
                        { label: '> 5 Juta', range: '> 5 Juta' }
                    ];
                    salaryRanges.forEach(item => {
                        const color = getDominantSalaryColor(item.range, 10);
                        legendContent += `<div style="display:flex;align-items:center;margin:1px 0;font-size:10px;">
                            <i style="background:${color};width:8px;height:8px;margin-right:3px;opacity:0.8;border-radius:1px;"></i>
                            ${item.label}
                        </div>`;
                    });
                    legendContent += '</div>';
                }

                if (activeTypes.includes('job_status')) {
                    legendContent += '<div style="margin-bottom:4px;"><strong style="font-size:11px;">Status Pekerjaan:</strong></div>';
                    legendContent += '<div style="margin-bottom:6px;margin-left:8px;">';
                    const jobStatusList = ['Bekerja', 'Belum bekerja', 'Melanjutkan pendidikan'];
                    jobStatusList.forEach(status => {
                        const color = getJobStatusColorByName(status, 10);
                        legendContent += `<div style="display:flex;align-items:center;margin:1px 0;font-size:10px;">
                            <i style="background:${color};width:8px;height:8px;margin-right:3px;opacity:0.8;border-radius:1px;"></i>
                            ${status}
                        </div>`;
                    });
                    legendContent += '</div>';
                }

                if (activeTypes.includes('job')) {
                    legendContent += '<div style="margin-bottom:4px;"><strong style="font-size:11px;">Pekerjaan:</strong></div>';
                    legendContent += '<div style="margin-bottom:6px;margin-left:8px;font-size:9px;color:#666;">Warna bervariasi per jenis pekerjaan</div>';
                }

                legendContent += '<div style="margin-top:8px;padding-top:6px;border-top:1px solid #eee;font-size:9px;color:#666;text-align:center;">Warna peta = kategori dengan data terbanyak per area</div>';
            }

            console.log('Final legend content length:', legendContent.length);

            contentDiv.innerHTML = legendContent;

            console.log('Legend updated successfully');
        };

        // Call update initially to populate content
        setTimeout(() => {
            self.update();
        }, 100);

        return div;
    }
});
L.control.legend = function (opts) { return new L.Control.Legend(opts); };

// Fungsi untuk membuat choropleth map alumni berdasarkan status kerja
function createJobStatusLayer(data, areaType) {
    return L.geoJSON(null, {
        style: function (feature) {
            const locationName = areaType === 'province' ? feature.properties.NAME_1 : feature.properties.NAME_2;
            const jobStatusStats = getJobStatusStats(data, locationName, areaType);
            const dominant = getDominantJobStatus(jobStatusStats);
            const total = Object.values(jobStatusStats).reduce((sum, v) => sum + v.jumlah, 0);
            return {
                fillColor: getJobStatusColorByName(dominant, total),
                weight: 1,
                opacity: 1,
                color: 'white',
                fillOpacity: 0.7
            };
        },
        onEachFeature: function (feature, layer) {
            const locationName = areaType === 'province' ? feature.properties.NAME_1 : feature.properties.NAME_2;
            const jobStatusStats = getJobStatusStats(data, locationName, areaType);
            const dominant = getDominantJobStatus(jobStatusStats);
            const total = Object.values(jobStatusStats).reduce((sum, v) => sum + v.jumlah, 0);
            const locLabel = areaType === 'province'
                ? feature.properties.NAME_1
                : `${feature.properties.NAME_2}, ${feature.properties.NAME_1}`;
            // Chart block
            const statusLabels = ['Bekerja', 'Wirausaha', 'Studi Lanjut', 'Mencari Kerja', 'Belum memungkinkan bekerja'];
            const percents = statusLabels.map(label => total > 0 ? (jobStatusStats[label].jumlah / total * 100) : 0);
            const chartId = `jobStatusOnlyChart_${locationName.replace(/\s+/g, '_')}`;
            let popupContent = `<strong>${locLabel}</strong><br>Dominan: <b>${dominant}</b><br><br>`;
            popupContent += `<div style='width:100%;max-width:420px;margin:8px auto 8px auto;'>
                <div style='font-weight:bold;font-size:15px;text-align:center;margin-bottom:4px;'>Distribusi Status Pekerjaan</div>
                <canvas id='${chartId}' height='${statusLabels.length * 32}' data-labels='${JSON.stringify(statusLabels)}' data-data='${JSON.stringify(percents)}'></canvas>
            </div>`;
            popupContent += `<div style='overflow-x:auto;'><table style='width:100%;max-width:100%;font-size:13px;text-align:left;border-collapse:collapse;'>`;
            popupContent += `<tr style='background:#f5f6fa;font-weight:bold;font-size:14px;'><th style='padding:6px 4px;text-align:left;'>Status Pekerjaan</th><th style='padding:6px 4px;text-align:center;'>Persentase</th><th style='padding:6px 4px;text-align:center;'>Jumlah Alumni</th></tr>`;
            Object.entries(jobStatusStats).forEach(([status, val]) => {
                popupContent += `<tr style='border-bottom:1px solid #eee;'><td style='padding:4px 4px;'>${status}</td><td style='text-align:center;padding:4px 4px;'>${val.persentase.toFixed(2)}%</td><td style='text-align:center;padding:4px 4px;'>${val.jumlah} orang</td></tr>`;
            });
            popupContent += `</table></div>`;
            layer.bindPopup(popupContent);
            layer.on({
                mouseover: function (e) {
                    var l = e.target;
                    l.setStyle({
                        weight: 3,
                        color: '#666',
                        dashArray: '',
                        fillOpacity: 1
                    });
                },
                mouseout: function (e) {
                    layer.setStyle({
                        weight: 1,
                        color: 'white',
                        fillOpacity: 0.7
                    });
                }
            });
        }
    });
}

function resetFilters() {
    try {
        // Reset all select dropdowns
        ['#filterName', '#companySelect', '#provinceSelect', '#citySelect',
            '#yearSelect', '#jobSelect', '#jobStatusSelect'].forEach(selector => {
                $(selector).val(null).trigger('change');
            });

        // Reset gender filter
        document.querySelectorAll('input[name="genderFilter"]').forEach(cb => {
            cb.checked = false;
        });

        // Close filter popup
        document.getElementById("filterPopup").style.display = "none";

        // Reset map view
        map.setView(initialView.center, initialView.zoom);
        window.activeLayer = null;

        // Reset table and map
        renderTable(alumniData);

        // Remove existing layers
        if (window.choroplethLayer) {
            map.removeLayer(window.choroplethLayer);
        }
        if (window.alumniMarkersCluster) {
            map.removeLayer(window.alumniMarkersCluster);
        }

        // Remove all layer types
        [
            'cityLayer', 'provinceLayer',
            'workingLayerCity', 'workingLayerProvince',
            'notWorkingLayerCity', 'notWorkingLayerProvince'
        ].forEach(l => {
            if (window[l]) {
                map.removeLayer(window[l]);
            }
        });

        // Remove legends
        if (window.legend) {
            map.removeControl(window.legend);
            window.legend = null;
        }
        document.querySelectorAll('.info.legend').forEach(function (legendElement) {
            if (legendElement && legendElement.parentNode) {
                legendElement.parentNode.removeChild(legendElement);
            }
        });

        // Remove layer controls
        if (window.layerControl) {
            map.removeControl(window.layerControl);
        }

        // Recreate and add layers
        updateMap(alumniData);

        // Ensure marker visibility
        const markerToggle = document.getElementById('markerToggle');
        if (markerToggle && markerToggle.checked && window.alumniMarkersCluster) {
            map.addLayer(window.alumniMarkersCluster);
        }

        // Make sure city layer is visible
        if (window.cityLayer) {
            map.addLayer(window.cityLayer);
        }

        // Recreate layer control
        window.layerControl = L.control.layerControl().addTo(map);

        console.log('Reset filters completed successfully');
    } catch (error) {
        console.error('Error during reset:', error);
    }
}

// Add new layer creation functions
function createCompanyTypeLayer(data, areaType) {
    return L.geoJSON(null, {
        style: function (feature) {
            const locationName = areaType === 'province' ? feature.properties.NAME_1 : feature.properties.NAME_2;
            const stats = getCompanyTypeStats(data, locationName, areaType);
            const dominant = getDominantCompanyType(stats);
            const total = Object.values(stats).reduce((sum, v) => sum + v.jumlah, 0);
            return {
                fillColor: getCompanyTypeColorByName(dominant, total),
                weight: 1,
                opacity: 1,
                color: 'white',
                fillOpacity: 0.7
            };
        },
        onEachFeature: function (feature, layer) {
            const locationName = areaType === 'province' ? feature.properties.NAME_1 : feature.properties.NAME_2;
            const stats = getCompanyTypeStats(data, locationName, areaType);
            const dominant = getDominantCompanyType(stats);
            const total = Object.values(stats).reduce((sum, v) => sum + v.jumlah, 0);
            const locLabel = areaType === 'province'
                ? feature.properties.NAME_1
                : `${feature.properties.NAME_2}, ${feature.properties.NAME_1}`;
            let popupContent = `<strong>${locLabel}</strong><br>Dominan: <b>${dominant}</b><br><br>`;
            popupContent += `<table style='width:100%;font-size:13px;text-align:left;'>`;
            popupContent += `<tr><th>Jenis Perusahaan</th><th>Persentase</th><th>Jumlah Alumni</th></tr>`;
            Object.entries(stats).forEach(([type, val]) => {
                popupContent += `<tr><td>${type}</td><td>${val.persentase.toFixed(2)}%<div style='background:#eee;width:100%;height:8px;border-radius:4px;margin-top:2px;'><div style='width:${val.persentase}%;background:${getCompanyTypeColorByName(type, total)};height:100%;border-radius:4px;'></div></div></td><td>${val.jumlah} orang</td></tr>`;
            });
            popupContent += `</table>`;
            layer.bindPopup(popupContent);
            layer.on({
                mouseover: function (e) {
                    var l = e.target;
                    l.setStyle({
                        weight: 3,
                        color: '#666',
                        dashArray: '',
                        fillOpacity: 1
                    });
                },
                mouseout: function (e) {
                    layer.setStyle({
                        weight: 1,
                        color: 'white',
                        fillOpacity: 0.7
                    });
                }
            });
        }
    });
}

function getSalaryRanges(data, locationName, areaType) {
    // Returns { '< 3 Juta': {jumlah, persentase}, ... }
    const filtered = data.filter(a =>
        areaType === 'province'
            ? normalizeName(a.province) === normalizeName(locationName)
            : normalizeName(a.city) === normalizeName(locationName)
    );
    const total = filtered.length;
    const ranges = {
        '< 3 Juta': 0,
        '3 Juta - 5 Juta': 0,
        '> 5 Juta': 0
    };
    filtered.forEach(a => {
        const gaji = a.salary || 0;
        if (gaji < 3000000) ranges['< 3 Juta']++;
        else if (gaji >= 3000000 && gaji <= 5000000) ranges['3 Juta - 5 Juta']++;
        else if (gaji > 5000000) ranges['> 5 Juta']++;
    });
    // Convert to {jumlah, persentase}
    const result = {};
    Object.entries(ranges).forEach(([k, v]) => {
        result[k] = {
            jumlah: v,
            persentase: total > 0 ? (v / total * 100) : 0
        };
    });
    return result;
}

function getDominantSalaryRange(salaryRanges) {
    // Returns the key with the highest jumlah
    let max = -1;
    let dominant = '< 3 Juta';
    Object.entries(salaryRanges).forEach(([k, v]) => {
        if (v.jumlah > max) {
            max = v.jumlah;
            dominant = k;
        }
    });
    return dominant;
}

function getDominantSalaryColor(dominantRange, total) {
    if (!total || total === 0) return '#CCCCCC';
    if (dominantRange === '< 3 Juta') return 'rgba(255, 99, 132, 0.7)';
    if (dominantRange === '3 Juta - 5 Juta') return 'rgba(54, 162, 235, 0.7)';
    if (dominantRange === '> 5 Juta') return 'rgba(255, 206, 86, 0.7)';
    return '#FFEDA0';
}

function getSalaryBarColor(range) {
    if (range === '< 3 Juta') return 'rgba(255, 99, 132, 0.7)';
    if (range === '3 Juta - 5 Juta') return 'rgba(54, 162, 235, 0.7)';
    if (range === '> 5 Juta') return 'rgba(255, 206, 86, 0.7)';
    return '#FFEDA0';
}

function createSalaryLayer(data, areaType) {
    console.log('createSalaryLayer called with data:', data.length, 'areaType:', areaType);
    return L.geoJSON(null, {
        style: function (feature) {
            const locationName = areaType === 'province' ? feature.properties.NAME_1 : feature.properties.NAME_2;
            const salaryRanges = getSalaryRanges(data, locationName, areaType);
            const dominant = getDominantSalaryRange(salaryRanges);
            const total = Object.values(salaryRanges).reduce((sum, v) => sum + v.jumlah, 0);
            console.log('Salary layer style for', locationName, '- dominant:', dominant, 'total:', total);
            return {
                fillColor: getDominantSalaryColor(dominant, total),
                weight: 1,
                opacity: 1,
                color: 'white',
                fillOpacity: 0.7
            };
        },
        onEachFeature: function (feature, layer) {
            const locationName = areaType === 'province' ? feature.properties.NAME_1 : feature.properties.NAME_2;
            const salaryRanges = getSalaryRanges(data, locationName, areaType);
            const dominant = getDominantSalaryRange(salaryRanges);
            const total = Object.values(salaryRanges).reduce((sum, v) => sum + v.jumlah, 0);
            const locLabel = areaType === 'province'
                ? feature.properties.NAME_1
                : `${feature.properties.NAME_2}, ${feature.properties.NAME_1}`;

            // Chart block
            const chartId = `salaryOnlyChart_${locationName.replace(/\s+/g, '_')}`;
            const percents = [
                salaryRanges["< 3 Juta"].persentase,
                salaryRanges["3 Juta - 5 Juta"].persentase,
                salaryRanges["> 5 Juta"].persentase
            ];

            let popupContent = `<strong>${locLabel}</strong><br>Dominan: <b>${dominant}</b><br><br>`;
            popupContent += `<div style='width:100%;max-width:320px;margin:16px auto 8px auto;'>
                <div style='font-weight:bold;font-size:15px;text-align:center;margin-bottom:4px;'>Distribusi Penghasilan Alumni</div>
                <canvas id='${chartId}' height='180' data-labels='${JSON.stringify(["< 3 Juta", "3 Juta - 5 Juta", "> 5 Juta"])}' data-data='${JSON.stringify(percents)}'></canvas>
            </div>`;
            popupContent += `<div style='overflow-x:auto;'><table style='width:100%;max-width:100%;font-size:13px;text-align:left;border-collapse:collapse;'>`;
            popupContent += `<tr style='background:#f5f6fa;font-weight:bold;font-size:14px;'><th style='padding:6px 4px;'>Range Penghasilan</th><th style='padding:6px 4px;text-align:center;'>Persentase</th><th style='padding:6px 4px;text-align:center;'>Jumlah Alumni</th></tr>`;
            Object.entries(salaryRanges).forEach(([range, val]) => {
                popupContent += `<tr style='border-bottom:1px solid #eee;'><td style='padding:4px 4px;'>${range}</td><td style='text-align:center;padding:4px 4px;'>${val.persentase.toFixed(2)}%</td><td style='text-align:center;padding:4px 4px;'>${val.jumlah} orang</td></tr>`;
            });
            popupContent += `</table></div>`;
            layer.bindPopup(popupContent);
            layer.on({
                mouseover: function (e) {
                    var l = e.target;
                    l.setStyle({
                        weight: 3,
                        color: '#666',
                        dashArray: '',
                        fillOpacity: 1
                    });
                },
                mouseout: function (e) {
                    layer.setStyle({
                        weight: 1,
                        color: 'white',
                        fillOpacity: 0.7
                    });
                }
            });
        }
    });
}

function createJobLayer(data, areaType) {
    console.log('createJobLayer called with data:', data.length, 'areaType:', areaType);
    return L.geoJSON(null, {
        style: function (feature) {
            const locationName = areaType === 'province' ? feature.properties.NAME_1 : feature.properties.NAME_2;
            const jobStats = getJobStats(data, locationName, areaType);
            const dominant = getDominantJob(jobStats);
            const total = Object.values(jobStats).reduce((sum, v) => sum + v.jumlah, 0);
            console.log('Job layer style for', locationName, '- dominant:', dominant, 'total:', total);
            return {
                fillColor: getJobColorByName(dominant, total),
                weight: 1,
                opacity: 1,
                color: 'white',
                fillOpacity: 0.7
            };
        },
        onEachFeature: function (feature, layer) {
            const locationName = areaType === 'province' ? feature.properties.NAME_1 : feature.properties.NAME_2;
            const jobStats = getJobStats(data, locationName, areaType);
            const dominant = getDominantJob(jobStats);
            const total = Object.values(jobStats).reduce((sum, v) => sum + v.jumlah, 0);
            const locLabel = areaType === 'province'
                ? feature.properties.NAME_1
                : `${feature.properties.NAME_2}, ${feature.properties.NAME_1}`;

            // Chart block
            const jobLabels = Object.keys(jobStats).filter(job => jobStats[job].jumlah > 0);
            const jobPercents = jobLabels.map(job => jobStats[job].persentase);
            const jobColors = jobLabels.map(job => getJobColorByName(job, total));
            const chartId = `jobOnlyChart_${locationName.replace(/\s+/g, '_')}`;
            const chartHeight = Math.max(jobLabels.length * 32, 48);

            let popupContent = `<strong>${locLabel}</strong><br>Dominan: <b>${dominant}</b><br><br>`;
            popupContent += `<div style='width:100%;max-width:420px;margin:8px auto 16px auto;'>
                <div style='font-weight:bold;font-size:15px;text-align:center;margin-bottom:4px;'>Distribusi Pekerjaan</div>
                <canvas id='${chartId}' height='${chartHeight}' data-labels='${JSON.stringify(jobLabels)}' data-data='${JSON.stringify(jobPercents)}' data-colors='${JSON.stringify(jobColors)}'></canvas>
            </div>`;
            popupContent += `<div style='overflow-x:auto;'><table style='width:100%;max-width:100%;font-size:13px;text-align:left;border-collapse:collapse;'>`;
            popupContent += `<tr style='background:#f5f6fa;font-weight:bold;font-size:14px;'><th style='padding:6px 4px;'>Pekerjaan Saat Ini</th><th style='padding:6px 4px;text-align:center;'>Persentase</th><th style='padding:6px 4px;text-align:center;'>Jumlah Alumni</th></tr>`;
            Object.entries(jobStats)
                .filter(([job, val]) => val.jumlah > 0)
                .sort((a, b) => b[1].jumlah - a[1].jumlah)
                .forEach(([job, val]) => {
                    popupContent += `<tr style='border-bottom:1px solid #eee;'><td style='padding:4px 4px;'>${job}</td><td style='text-align:center;padding:4px 4px;'>${val.persentase.toFixed(2)}%</td><td style='text-align:center;padding:4px 4px;'>${val.jumlah} orang</td></tr>`;
                });
            popupContent += `</table></div>`;
            layer.bindPopup(popupContent);
            layer.on({
                mouseover: function (e) {
                    var l = e.target;
                    l.setStyle({
                        weight: 3,
                        color: '#666',
                        dashArray: '',
                        fillOpacity: 1
                    });
                },
                mouseout: function (e) {
                    layer.setStyle({
                        weight: 1,
                        color: 'white',
                        fillOpacity: 0.7
                    });
                }
            });
        }
    });
}

function createAlumniCountLayer(data, areaType) {
    return L.geoJSON(null, {
        style: function (feature) {
            const locationName = areaType === 'province' ? feature.properties.NAME_1 : feature.properties.NAME_2;
            const filtered = data.filter(a =>
                areaType === 'province'
                    ? normalizeName(a.province) === normalizeName(locationName)
                    : normalizeName(a.city) === normalizeName(locationName)
            );
            const count = filtered.length;
            return {
                fillColor: getColor(count),
                weight: 1,
                opacity: 1,
                color: 'white',
                fillOpacity: 0.7
            };
        },
        onEachFeature: function (feature, layer) {
            const locationName = areaType === 'province' ? feature.properties.NAME_1 : feature.properties.NAME_2;
            const filtered = data.filter(a =>
                areaType === 'province'
                    ? normalizeName(a.province) === normalizeName(locationName)
                    : normalizeName(a.city) === normalizeName(locationName)
            );
            const count = filtered.length;
            const locLabel = areaType === 'province'
                ? feature.properties.NAME_1
                : `${feature.properties.NAME_2}, ${feature.properties.NAME_1}`;

            // Chart block
            const chartId = `alumniCountOnlyChart_${locationName.replace(/\s+/g, '_')}`;

            let popupContent = `<strong>${locLabel}</strong><br>Jumlah Alumni: <b>${count}</b><br><br>`;
            popupContent += `<div style='width:100%;max-width:220px;margin:16px auto 8px auto;'>
                <div style='font-weight:bold;font-size:15px;text-align:center;margin-bottom:4px;'>Jumlah Alumni</div>
                <canvas id='${chartId}' height='180' data-labels='${JSON.stringify(["Jumlah Alumni"])}' data-data='${JSON.stringify([count])}'></canvas>
            </div>`;

            if (areaType === 'province') {
                const cityCounts = {};
                filtered.forEach(a => {
                    const city = a.city || 'Tidak Diketahui';
                    cityCounts[city] = (cityCounts[city] || 0) + 1;
                });

                popupContent += `<div style='margin-top:10px; font-weight:bold; font-size:14px; text-align:center;'>Distribusi per Kota/Kabupaten</div>`;
                popupContent += `<div style='overflow-y:auto; max-height: 150px; margin-top: 5px;'><table style='width:100%;font-size:13px;text-align:left;border-collapse:collapse;'>`;
                popupContent += `<thead style='position:sticky;top:0;background:white;'><tr style='background:#f5f6fa;font-weight:bold;font-size:14px;'><th style='padding:6px 4px;'>Kota/Kab.</th><th style='padding:6px 4px;text-align:center;'>Jumlah</th></tr></thead>`;
                popupContent += `<tbody>`;
                Object.entries(cityCounts).sort((a, b) => b[1] - a[1]).forEach(([city, jumlah]) => {
                    popupContent += `<tr style='border-bottom:1px solid #eee;'><td style='padding:4px 4px;'>${city}</td><td style='text-align:center;padding:4px 4px;'>${jumlah}</td></tr>`;
                });
                popupContent += `</tbody></table></div>`;
            } else {
                popupContent += `<div style='overflow-x:auto;'><table style='width:100%;max-width:100%;font-size:13px;text-align:left;border-collapse:collapse;'><tr style='background:#f5f6fa;font-weight:bold;font-size:14px;'><th style='padding:6px 4px;'>Area</th><th style='padding:6px 4px;text-align:center;'>Jumlah</th></tr><tr><td style='padding:4px 4px;'>${locationName}</td><td style='text-align:center;padding:4px 4px;'>${count}</td></tr></table></div>`;
            }

            layer.bindPopup(popupContent);
            layer.on({
                mouseover: function (e) {
                    var l = e.target;
                    l.setStyle({
                        weight: 3,
                        color: '#666',
                        dashArray: '',
                        fillOpacity: 1
                    });
                },
                mouseout: function (e) {
                    layer.setStyle({
                        weight: 1,
                        color: 'white',
                        fillOpacity: 0.7
                    });
                }
            });
        }
    });
}

// Helper functions for new layers
function getCompanyTypes(data, locationName, areaType) {
    const companies = data.filter(a =>
        areaType === 'province' ?
            normalizeName(a.province) === normalizeName(locationName) :
            normalizeName(a.city) === normalizeName(locationName)
    ).map(a => a.company_type || 'Unknown');

    return companies.reduce((acc, type) => {
        acc[type] = (acc[type] || 0) + 1;
        return acc;
    }, {});
}

function getCompanyTypeColorByName(type, total) {
    if (!total || total === 0) return '#CCCCCC';
    return COMPANY_TYPE_COLORS[type] || '#CCCCCC';
}

// Company type label mapping (should match backend)
const COMPANY_TYPE_LABELS = {
    1: 'Instansi pemerintah',
    2: 'BUMN/BUMD',
    3: 'Institusi/Organisasi Multilateral',
    4: 'Organisasi non-profit/LSM',
    5: 'Perusahaan swasta',
    6: 'Wiraswasta/perusahaan sendiri',
    7: 'Lainnya',
};

function getCompanyTypeStats(data, locationName, areaType) {
    // Returns { label: {jumlah, persentase} }
    const filtered = data.filter(a =>
        areaType === 'province'
            ? normalizeName(a.province) === normalizeName(locationName)
            : normalizeName(a.city) === normalizeName(locationName)
    );
    const total = filtered.length;
    const counts = {};
    filtered.forEach(a => {
        let type = a.company_type;
        // Map numeric type to label if needed
        if (typeof type === 'number' || (typeof type === 'string' && COMPANY_TYPE_LABELS[type])) {
            type = COMPANY_TYPE_LABELS[type];
        }
        if (!type || !Object.values(COMPANY_TYPE_LABELS).includes(type)) type = 'Unknown';
        counts[type] = (counts[type] || 0) + 1;
    });
    // Ensure all types are present
    Object.values(COMPANY_TYPE_LABELS).forEach(label => {
        if (!counts[label]) counts[label] = 0;
    });
    if (!counts['Unknown']) counts['Unknown'] = 0;
    // Convert to {jumlah, persentase}
    const result = {};
    Object.entries(counts).forEach(([k, v]) => {
        result[k] = {
            jumlah: v,
            persentase: total > 0 ? (v / total * 100) : 0
        };
    });
    return result;
}

function getDominantCompanyType(stats) {
    let max = -1;
    let dominant = 'Unknown';
    Object.entries(stats).forEach(([k, v]) => {
        if (v.jumlah > max) {
            max = v.jumlah;
            dominant = k;
        }
    });
    return dominant;
}

// Company type color mapping
const COMPANY_TYPE_COLORS = {
    'Instansi pemerintah': '#36A2EB',
    'BUMN/BUMD': '#FFCE56',
    'Institusi/Organisasi Multilateral': '#4BC0C0',
    'Organisasi non-profit/LSM': '#FF6384',
    'Perusahaan swasta': '#9966FF',
    'Wiraswasta/perusahaan sendiri': '#8BC34A',
    'Lainnya': '#BDBDBD',
    'Unknown': '#CCCCCC'
};

function getCompanyTypeColor(companyTypes) {
    // Implement color scheme based on company types
    return '#1f77b4'; // Default color
}

// Helper function untuk membuat/update legend
function createAndUpdateLegend() {
    if (window.legend) map.removeControl(window.legend);
    window.legend = L.control.legend().addTo(map);
    // Update legend content setelah dibuat dengan delay yang cukup
    setTimeout(() => {
        if (window.legend && window.legend.update) {
            console.log('Calling legend update from helper');
            window.legend.update();
        } else {
            console.log('Legend or update method not available:', !!window.legend, !!(window.legend && window.legend.update));
        }
    }, 200);
}

// Event popupopen: render chart alumni count, company, salary, company+salary
map.on('popupopen', function (e) {
    // Alumni count chart (vertical bar)
    e.popup.getElement().querySelectorAll('canvas[id^="alumniCountChart_"]').forEach(function (canvas) {
        if (canvas.dataset.rendered) return;
        const labels = JSON.parse(canvas.getAttribute('data-labels'));
        const data = JSON.parse(canvas.getAttribute('data-data'));
        function renderAlumniCountChart() {
            const ctx = canvas.getContext('2d');
            new window.Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Jumlah Alumni',
                        data: data,
                        backgroundColor: ['#36A2EB'],
                        borderRadius: 8,
                        hoverBackgroundColor: ['#1976d2']
                    }]
                },
                options: {
                    responsive: true,
                    animation: {
                        duration: 1200,
                        easing: 'easeOutBounce'
                    },
                    plugins: {
                        legend: { display: false },
                        tooltip: { enabled: true }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { stepSize: 1 }
                        }
                    },
                    hover: {
                        mode: 'nearest',
                        intersect: true
                    }
                }
            });
            canvas.dataset.rendered = '1';
        }
        if (!window.Chart) {
            var script = document.createElement('script');
            script.src = 'https://cdn.jsdelivr.net/npm/chart.js';
            script.onload = renderAlumniCountChart;
            document.body.appendChild(script);
        } else {
            renderAlumniCountChart();
        }
    });
    // Company chart (horizontal bar)
    e.popup.getElement().querySelectorAll('canvas[id^="companyChart_"]').forEach(function (canvas) {
        if (canvas.dataset.rendered) return;
        const labels = JSON.parse(canvas.getAttribute('data-labels'));
        const data = JSON.parse(canvas.getAttribute('data-data'));
        const colors = JSON.parse(canvas.getAttribute('data-colors'));
        function renderCompanyChart() {
            const ctx = canvas.getContext('2d');
            new window.Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Persentase',
                        data: data,
                        backgroundColor: colors,
                        borderRadius: 8,
                        hoverBackgroundColor: colors.map(c => c.replace('0.7', '1'))
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    animation: {
                        duration: 1200,
                        easing: 'easeOutBounce'
                    },
                    plugins: {
                        legend: { display: false },
                        tooltip: { enabled: true }
                    },
                    scales: {
                        x: {
                            beginAtZero: true,
                            max: 100,
                            ticks: { callback: function (v) { return v + '%'; } }
                        }
                    },
                    hover: {
                        mode: 'nearest',
                        intersect: true
                    }
                }
            });
            canvas.dataset.rendered = '1';
        }
        if (!window.Chart) {
            var script = document.createElement('script');
            script.src = 'https://cdn.jsdelivr.net/npm/chart.js';
            script.onload = renderCompanyChart;
            document.body.appendChild(script);
        } else {
            renderCompanyChart();
        }
    });
    // Salary chart (vertical bar)
    e.popup.getElement().querySelectorAll('canvas[id^="salaryChart_"]').forEach(function (canvas) {
        if (canvas.dataset.rendered) return;
        const labels = JSON.parse(canvas.getAttribute('data-labels'));
        const data = JSON.parse(canvas.getAttribute('data-data'));
        function renderSalaryChart() {
            const ctx = canvas.getContext('2d');
            new window.Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Persentase',
                        data: data,
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.7)',
                            'rgba(54, 162, 235, 0.7)',
                            'rgba(255, 206, 86, 0.7)'
                        ],
                        borderRadius: 8,
                        hoverBackgroundColor: [
                            'rgba(255, 99, 132, 1)',
                            'rgba(54, 162, 235, 1)',
                            'rgba(255, 206, 86, 1)'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    animation: {
                        duration: 1200,
                        easing: 'easeOutBounce'
                    },
                    plugins: {
                        legend: { display: false },
                        tooltip: { enabled: true }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100,
                            ticks: { callback: function (v) { return v + '%'; } }
                        }
                    },
                    hover: {
                        mode: 'nearest',
                        intersect: true
                    }
                }
            });
            canvas.dataset.rendered = '1';
        }
        if (!window.Chart) {
            var script = document.createElement('script');
            script.src = 'https://cdn.jsdelivr.net/npm/chart.js';
            script.onload = renderSalaryChart;
            document.body.appendChild(script);
        } else {
            renderSalaryChart();
        }
    });
    // Company+Salary stacked bar chart (horizontal)
    e.popup.getElement().querySelectorAll('canvas[id^="companySalaryChart_"]').forEach(function (canvas) {
        if (canvas.dataset.rendered) return;
        const labels = JSON.parse(canvas.getAttribute('data-labels'));
        const salaryLabels = JSON.parse(canvas.getAttribute('data-salary-labels'));
        const data = JSON.parse(canvas.getAttribute('data-data'));
        function renderCompanySalaryChart() {
            const ctx = canvas.getContext('2d');
            new window.Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: salaryLabels.map((slabel, i) => ({
                        label: slabel,
                        data: data[i],
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.7)',
                            'rgba(54, 162, 235, 0.7)',
                            'rgba(255, 206, 86, 0.7)'
                        ][i],
                        borderRadius: 8,
                        hoverBackgroundColor: [
                            'rgba(255, 99, 132, 1)',
                            'rgba(54, 162, 235, 1)',
                            'rgba(255, 206, 86, 1)'
                        ][i]
                    }))
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    animation: {
                        duration: 1200,
                        easing: 'easeOutBounce'
                    },
                    plugins: {
                        legend: { display: true },
                        tooltip: { enabled: true }
                    },
                    scales: {
                        x: {
                            beginAtZero: true,
                            max: 100,
                            stacked: true,
                            ticks: { callback: function (v) { return v + '%'; } }
                        },
                        y: {
                            stacked: true
                        }
                    },
                    hover: {
                        mode: 'nearest',
                        intersect: true
                    }
                }
            });
            canvas.dataset.rendered = '1';
        }
        if (!window.Chart) {
            var script = document.createElement('script');
            script.src = 'https://cdn.jsdelivr.net/npm/chart.js';
            script.onload = renderCompanySalaryChart;
            document.body.appendChild(script);
        } else {
            renderCompanySalaryChart();
        }
    });
    // Job status chart (horizontal bar)
    e.popup.getElement().querySelectorAll('canvas[id^="jobStatusChart_"]').forEach(function (canvas) {
        if (canvas.dataset.rendered) return;
        const labels = JSON.parse(canvas.getAttribute('data-labels'));
        const data = JSON.parse(canvas.getAttribute('data-data'));
        function renderJobStatusChart() {
            const ctx = canvas.getContext('2d');
            new window.Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Persentase',
                        data: data,
                        backgroundColor: labels.map(l => JOB_STATUS_COLORS[l] || '#CCCCCC'),
                        borderRadius: 8,
                        hoverBackgroundColor: labels.map(l => (JOB_STATUS_COLORS[l] || '#CCCCCC').replace('0.7', '1'))
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    animation: {
                        duration: 1200,
                        easing: 'easeOutBounce'
                    },
                    plugins: {
                        legend: { display: false },
                        tooltip: { enabled: true }
                    },
                    scales: {
                        x: {
                            beginAtZero: true,
                            max: 100,
                            ticks: { callback: function (v) { return v + '%'; } }
                        }
                    },
                    hover: {
                        mode: 'nearest',
                        intersect: true
                    }
                }
            });
            canvas.dataset.rendered = '1';
        }
        if (!window.Chart) {
            var script = document.createElement('script');
            script.src = 'https://cdn.jsdelivr.net/npm/chart.js';
            script.onload = renderJobStatusChart;
            document.body.appendChild(script);
        } else {
            renderJobStatusChart();
        }
    });
    // Company+JobStatus stacked bar chart (horizontal)
    e.popup.getElement().querySelectorAll('canvas[id^="companyJobStatusChart_"]').forEach(function (canvas) {
        if (canvas.dataset.rendered) return;
        const labels = JSON.parse(canvas.getAttribute('data-labels'));
        const statusLabels = JSON.parse(canvas.getAttribute('data-status-labels'));
        const data = JSON.parse(canvas.getAttribute('data-data'));
        function renderCompanyJobStatusChart() {
            const ctx = canvas.getContext('2d');
            new window.Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: statusLabels.map((slabel, i) => ({
                        label: slabel,
                        data: data[i],
                        backgroundColor: JOB_STATUS_COLORS[slabel] || '#CCCCCC',
                        borderRadius: 8,
                        hoverBackgroundColor: (JOB_STATUS_COLORS[slabel] || '#CCCCCC').replace('0.7', '1')
                    }))
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    animation: {
                        duration: 1200,
                        easing: 'easeOutBounce'
                    },
                    plugins: {
                        legend: { display: true },
                        tooltip: { enabled: true }
                    },
                    scales: {
                        x: {
                            beginAtZero: true,
                            max: 100,
                            stacked: true,
                            ticks: { callback: function (v) { return v + '%'; } }
                        },
                        y: {
                            stacked: true
                        }
                    },
                    hover: {
                        mode: 'nearest',
                        intersect: true
                    }
                }
            });
            canvas.dataset.rendered = '1';
        }
        if (!window.Chart) {
            var script = document.createElement('script');
            script.src = 'https://cdn.jsdelivr.net/npm/chart.js';
            script.onload = renderCompanyJobStatusChart;
            document.body.appendChild(script);
        } else {
            renderCompanyJobStatusChart();
        }
    });
    // Cross-tab chart (horizontal stacked bar)
    e.popup.getElement().querySelectorAll('canvas[id^="crossTabChart_"]').forEach(function (canvas) {
        if (canvas.dataset.rendered) return;
        const xLabels = JSON.parse(canvas.getAttribute('data-labels'));
        const yLabels = JSON.parse(canvas.getAttribute('data-y-labels'));
        const data = JSON.parse(canvas.getAttribute('data-data'));
        function renderCrossTabChart() {
            const ctx = canvas.getContext('2d');
            new window.Chart(ctx, {
                type: 'bar',
                data: {
                    labels: yLabels,
                    datasets: xLabels.map((xlabel, i) => ({
                        label: xlabel,
                        data: data.map(row => row[i]),
                        backgroundColor: i < 3 ? [
                            'rgba(255, 99, 132, 0.7)',
                            'rgba(54, 162, 235, 0.7)',
                            'rgba(255, 206, 86, 0.7)'
                        ][i] : '#CCCCCC',
                        borderRadius: 8,
                        hoverBackgroundColor: i < 3 ? [
                            'rgba(255, 99, 132, 1)',
                            'rgba(54, 162, 235, 1)',
                            'rgba(255, 206, 86, 1)'
                        ][i] : '#CCCCCC'
                    }))
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    animation: {
                        duration: 1200,
                        easing: 'easeOutBounce'
                    },
                    plugins: {
                        legend: { display: true },
                        tooltip: { enabled: true }
                    },
                    scales: {
                        x: {
                            beginAtZero: true,
                            max: 100,
                            stacked: true,
                            ticks: { callback: function (v) { return v + '%'; } }
                        },
                        y: {
                            stacked: true
                        }
                    },
                    hover: {
                        mode: 'nearest',
                        intersect: true
                    }
                }
            });
            canvas.dataset.rendered = '1';
        }
        if (!window.Chart) {
            var script = document.createElement('script');
            script.src = 'https://cdn.jsdelivr.net/npm/chart.js';
            script.onload = renderCrossTabChart;
            document.body.appendChild(script);
        } else {
            renderCrossTabChart();
        }
    });
    // Job status only chart (horizontal bar)
    e.popup.getElement().querySelectorAll('canvas[id^="jobStatusOnlyChart_"]').forEach(function (canvas) {
        if (canvas.dataset.rendered) return;
        const labels = JSON.parse(canvas.getAttribute('data-labels'));
        const data = JSON.parse(canvas.getAttribute('data-data'));
        function renderJobStatusOnlyChart() {
            const ctx = canvas.getContext('2d');
            new window.Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Persentase',
                        data: data,
                        backgroundColor: labels.map(l => JOB_STATUS_COLORS[l] || '#CCCCCC'),
                        borderRadius: 8,
                        hoverBackgroundColor: labels.map(l => (JOB_STATUS_COLORS[l] || '#CCCCCC').replace('0.7', '1'))
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    animation: {
                        duration: 1200,
                        easing: 'easeOutBounce'
                    },
                    plugins: {
                        legend: { display: false },
                        tooltip: { enabled: true }
                    },
                    scales: {
                        x: {
                            beginAtZero: true,
                            max: 100,
                            ticks: { callback: function (v) { return v + '%'; } }
                        }
                    },
                    hover: {
                        mode: 'nearest',
                        intersect: true
                    }
                }
            });
            canvas.dataset.rendered = '1';
        }
        if (!window.Chart) {
            var script = document.createElement('script');
            script.src = 'https://cdn.jsdelivr.net/npm/chart.js';
            script.onload = renderJobStatusOnlyChart;
            document.body.appendChild(script);
        } else {
            renderJobStatusOnlyChart();
        }
    });
    // Job bar chart (horizontal bar)
    e.popup.getElement().querySelectorAll('canvas[id^="jobChart_"]').forEach(function (canvas) {
        if (canvas.dataset.rendered) return;
        const labels = JSON.parse(canvas.getAttribute('data-labels'));
        const data = JSON.parse(canvas.getAttribute('data-data'));
        const colors = JSON.parse(canvas.getAttribute('data-colors'));
        function renderJobChart() {
            const ctx = canvas.getContext('2d');
            new window.Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Persentase',
                        data: data,
                        backgroundColor: colors,
                        borderRadius: 8,
                        hoverBackgroundColor: colors.map(c => c.replace('0.7', '1'))
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    animation: {
                        duration: 1200,
                        easing: 'easeOutBounce'
                    },
                    plugins: {
                        legend: { display: false },
                        tooltip: { enabled: true }
                    },
                    scales: {
                        x: {
                            beginAtZero: true,
                            max: 100,
                            ticks: { callback: function (v) { return v + '%'; } }
                        }
                    },
                    hover: {
                        mode: 'nearest',
                        intersect: true
                    }
                }
            });
            canvas.dataset.rendered = '1';
        }
        if (!window.Chart) {
            var script = document.createElement('script');
            script.src = 'https://cdn.jsdelivr.net/npm/chart.js';
            script.onload = renderJobChart;
            document.body.appendChild(script);
        } else {
            renderJobChart();
        }
    });
    // Salary only chart (vertical bar)
    e.popup.getElement().querySelectorAll('canvas[id^="salaryOnlyChart_"]').forEach(function (canvas) {
        if (canvas.dataset.rendered) return;
        const labels = JSON.parse(canvas.getAttribute('data-labels'));
        const data = JSON.parse(canvas.getAttribute('data-data'));
        function renderSalaryOnlyChart() {
            const ctx = canvas.getContext('2d');
            new window.Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Persentase',
                        data: data,
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.7)',
                            'rgba(54, 162, 235, 0.7)',
                            'rgba(255, 206, 86, 0.7)'
                        ],
                        borderRadius: 8,
                        hoverBackgroundColor: [
                            'rgba(255, 99, 132, 1)',
                            'rgba(54, 162, 235, 1)',
                            'rgba(255, 206, 86, 1)'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    animation: {
                        duration: 1200,
                        easing: 'easeOutBounce'
                    },
                    plugins: {
                        legend: { display: false },
                        tooltip: { enabled: true }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100,
                            ticks: { callback: function (v) { return v + '%'; } }
                        }
                    },
                    hover: {
                        mode: 'nearest',
                        intersect: true
                    }
                }
            });
            canvas.dataset.rendered = '1';
        }
        if (!window.Chart) {
            var script = document.createElement('script');
            script.src = 'https://cdn.jsdelivr.net/npm/chart.js';
            script.onload = renderSalaryOnlyChart;
            document.body.appendChild(script);
        } else {
            renderSalaryOnlyChart();
        }
    });

    // Job only chart (horizontal bar)
    e.popup.getElement().querySelectorAll('canvas[id^="jobOnlyChart_"]').forEach(function (canvas) {
        if (canvas.dataset.rendered) return;
        const labels = JSON.parse(canvas.getAttribute('data-labels'));
        const data = JSON.parse(canvas.getAttribute('data-data'));
        const colors = JSON.parse(canvas.getAttribute('data-colors'));
        function renderJobOnlyChart() {
            const ctx = canvas.getContext('2d');
            new window.Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Persentase',
                        data: data,
                        backgroundColor: colors,
                        borderRadius: 8,
                        hoverBackgroundColor: colors.map(c => c.replace('0.7', '1'))
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    animation: {
                        duration: 1200,
                        easing: 'easeOutBounce'
                    },
                    plugins: {
                        legend: { display: false },
                        tooltip: { enabled: true }
                    },
                    scales: {
                        x: {
                            beginAtZero: true,
                            max: 100,
                            ticks: { callback: function (v) { return v + '%'; } }
                        }
                    },
                    hover: {
                        mode: 'nearest',
                        intersect: true
                    }
                }
            });
            canvas.dataset.rendered = '1';
        }
        if (!window.Chart) {
            var script = document.createElement('script');
            script.src = 'https://cdn.jsdelivr.net/npm/chart.js';
            script.onload = renderJobOnlyChart;
            document.body.appendChild(script);
        } else {
            renderJobOnlyChart();
        }
    });

    // Alumni count only chart (vertical bar)
    e.popup.getElement().querySelectorAll('canvas[id^="alumniCountOnlyChart_"]').forEach(function (canvas) {
        if (canvas.dataset.rendered) return;
        const labels = JSON.parse(canvas.getAttribute('data-labels'));
        const data = JSON.parse(canvas.getAttribute('data-data'));
        function renderAlumniCountOnlyChart() {
            const ctx = canvas.getContext('2d');
            new window.Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Jumlah Alumni',
                        data: data,
                        backgroundColor: ['#36A2EB'],
                        borderRadius: 8,
                        hoverBackgroundColor: ['#1976d2']
                    }]
                },
                options: {
                    responsive: true,
                    animation: {
                        duration: 1200,
                        easing: 'easeOutBounce'
                    },
                    plugins: {
                        legend: { display: false },
                        tooltip: { enabled: true }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { stepSize: 1 }
                        }
                    },
                    hover: {
                        mode: 'nearest',
                        intersect: true
                    }
                }
            });
            canvas.dataset.rendered = '1';
        }
        if (!window.Chart) {
            var script = document.createElement('script');
            script.src = 'https://cdn.jsdelivr.net/npm/chart.js';
            script.onload = renderAlumniCountOnlyChart;
            document.body.appendChild(script);
        } else {
            renderAlumniCountOnlyChart();
        }
    });

    // Setelah render semua chart:
    setTimeout(() => {
        if (window.Chart && window.Chart.instances) {
            Object.values(window.Chart.instances).forEach(chart => {
                if (chart && chart.resize) chart.resize();
            });
        }
    }, 200);
});

// Job status color mapping
const JOB_STATUS_COLORS = {
    'Bekerja': '#36A2EB',
    'Wirausaha': '#FFCE56',
    'Studi Lanjut': '#4BC0C0',
    'Mencari Kerja': '#FF6384',
    'Belum memungkinkan bekerja': '#BDBDBD',
    'Unknown': '#CCCCCC'
};

function getJobStatusStats(data, locationName, areaType) {
    // Returns { label: {jumlah, persentase} }
    const statusLabels = ['Bekerja', 'Wirausaha', 'Studi Lanjut', 'Mencari Kerja', 'Belum memungkinkan bekerja'];

    const filtered = data.filter(a =>
        areaType === 'province'
            ? normalizeName(a.province) === normalizeName(locationName)
            : normalizeName(a.city) === normalizeName(locationName)
    );
    const total = filtered.length;
    const counts = {};
    statusLabels.forEach(label => { counts[label] = 0; });
    filtered.forEach(a => {
        let s = a.status;
        if (typeof s === 'number' || (typeof s === 'string' && JOB_STATUS_MAP[s])) {
            s = JOB_STATUS_MAP[s];
        }
        if (!statusLabels.includes(s)) s = 'Belum memungkinkan bekerja';
        counts[s] = (counts[s] || 0) + 1;
    });
    // Convert to {jumlah, persentase}
    const result = {};
    Object.entries(counts).forEach(([k, v]) => {
        result[k] = {
            jumlah: v,
            persentase: total > 0 ? (v / total * 100) : 0
        };
    });
    return result;
}

function getDominantJobStatus(stats) {
    let max = -1;
    let dominant = 'Unknown';
    Object.entries(stats).forEach(([k, v]) => {
        if (v.jumlah > max) {
            max = v.jumlah;
            dominant = k;
        }
    });
    return dominant;
}

function getJobStatusColorByName(status, total) {
    if (!total || total === 0) return '#CCCCCC';
    return JOB_STATUS_COLORS[status] || '#CCCCCC';
}

function getJobStats(data, locationName, areaType) {
    const filtered = data.filter(a =>
        areaType === 'province'
            ? normalizeName(a.province) === normalizeName(locationName)
            : normalizeName(a.city) === normalizeName(locationName)
    );
    const total = filtered.length;
    const counts = {};

    filtered.forEach(a => {
        const job = a.job || 'Tidak Diketahui';
        counts[job] = (counts[job] || 0) + 1;
    });

    // Convert to {jumlah, persentase}
    const result = {};
    Object.entries(counts).forEach(([k, v]) => {
        result[k] = {
            jumlah: v,
            persentase: total > 0 ? (v / total * 100) : 0
        };
    });

    return result;
}

function getDominantJob(stats) {
    let max = -1;
    let dominant = 'Tidak Diketahui';
    Object.entries(stats).forEach(([k, v]) => {
        if (v.jumlah > max) {
            max = v.jumlah;
            dominant = k;
        }
    });
    return dominant;
}

function getJobLabelsFromData(data) {
    const jobCounts = {};
    data.forEach(a => {
        const job = a.job || 'Tidak Diketahui';
        if (job !== 'Tidak Diketahui') {
            jobCounts[job] = (jobCounts[job] || 0) + 1;
        }
    });

    // Sort by count and take top 10
    return Object.entries(jobCounts)
        .sort((a, b) => b[1] - a[1])
        .slice(0, 10)
        .map(([job, count]) => job);
}

function getJobColorByName(jobName, total) {
    if (!total || total === 0) return '#CCCCCC';
    if (jobName === 'Tidak Diketahui') return '#CCCCCC';

    // Hash function untuk warna konsisten
    let hash = 0;
    for (let i = 0; i < jobName.length; i++) {
        hash = jobName.charCodeAt(i) + ((hash << 5) - hash);
    }

    // Enhanced colors - lebih bervariasi dan menarik
    const colors = [
        '#FF6B6B', '#4ECDC4', '#45B7D1', '#96CEB4', '#FECA57',  // Baris 1: Coral, Turquoise, Blue, Mint, Yellow
        '#FF9FF3', '#54A0FF', '#5F27CD', '#00D2D3', '#FF9F43',  // Baris 2: Pink, Blue, Purple, Cyan, Orange  
        '#FF6348', '#2ED573', '#3742FA', '#A55EEA', '#26C6DA',  // Baris 3: Red, Green, Blue, Purple, Cyan
        '#FFA502', '#FF3838', '#1B9CFC', '#55A3FF', '#FD79A8',  // Baris 4: Orange, Red, Blue, Sky, Pink
        '#FF7675', '#74B9FF', '#6C5CE7', '#FDCB6E', '#E17055'   // Baris 5: Salmon, Blue, Purple, Yellow, Brown
    ];

    return colors[Math.abs(hash) % colors.length];
}

function validateCombinations() {
    const checkedCategories = [];
    document.querySelectorAll('.category-checkbox').forEach((cb, idx) => {
        if (cb.checked) checkedCategories.push(categories[idx].type);
    });

    // Reset semua style
    document.querySelectorAll('.category-checkbox').forEach((cb, idx) => {
        const parentDiv = cb.parentElement;
        parentDiv.style.opacity = '1';
        parentDiv.style.pointerEvents = 'auto';
        parentDiv.title = '';
    });

    // Jika tidak ada yang dipilih, semua aktif
    if (checkedCategories.length === 0) {
        return;
    }

    // Definisi kombinasi yang diizinkan
    const allowedCombinations = [
        // Single kategori - semua diizinkan
        ['count'], ['company'], ['salary'], ['job_status'], ['job'],

        // Dua kategori - job_status hanya sendiri
        ['company', 'salary'],
        ['job', 'salary'], ['job', 'company'],

        // Tiga kategori - job_status tidak termasuk
        ['job', 'company', 'salary']
    ];

    // Cek apakah kombinasi saat ini diizinkan
    const currentCombination = checkedCategories.sort();
    const isValidCombination = allowedCombinations.some(allowed =>
        allowed.length === currentCombination.length &&
        allowed.sort().every((item, index) => item === currentCombination[index])
    );

    // Jika kombinasi tidak valid, disable kategori yang tidak kompatibel
    if (checkedCategories.length > 0) {
        document.querySelectorAll('.category-checkbox').forEach((cb, idx) => {
            if (!cb.checked) {
                const testCombination = [...checkedCategories, categories[idx].type].sort();
                const wouldBeValid = allowedCombinations.some(allowed =>
                    allowed.length === testCombination.length &&
                    allowed.sort().every((item, index) => item === testCombination[index])
                );

                if (!wouldBeValid) {
                    const parentDiv = cb.parentElement;
                    parentDiv.style.opacity = '0.5';
                    parentDiv.style.pointerEvents = 'none';
                    parentDiv.title = 'Kombinasi ini tidak tersedia';
                }
            }
        });
    }

    // Tambahkan info kombinasi yang sedang aktif
    let infoDiv = document.getElementById('combination-info');
    if (!infoDiv) {
        infoDiv = L.DomUtil.create('div', '', categoryBox);
        infoDiv.id = 'combination-info';
        infoDiv.style.marginTop = '10px';
        infoDiv.style.padding = '8px';
        infoDiv.style.borderRadius = '4px';
        infoDiv.style.fontSize = '11px';
    }

    if (checkedCategories.length === 0) {
        infoDiv.innerHTML = '<span style="color:#666;">💡 Pilih kategori untuk melihat peta tematik</span>';
        infoDiv.style.backgroundColor = '#f8f9fa';
        infoDiv.style.border = '1px solid #e9ecef';
    } else if (isValidCombination) {
        const combinationNames = checkedCategories.map(type =>
            categories.find(cat => cat.type === type)?.name || type
        ).join(' + ');
        infoDiv.innerHTML = `<span style="color:#28a745;">✓ ${combinationNames}</span>`;
        infoDiv.style.backgroundColor = '#d4edda';
        infoDiv.style.border = '1px solid #c3e6cb';
    } else {
        infoDiv.innerHTML = '<span style="color:#dc3545;">⚠️ Kombinasi tidak tersedia</span>';
        infoDiv.style.backgroundColor = '#f8d7da';
        infoDiv.style.border = '1px solid #f5c6cb';
    }
}