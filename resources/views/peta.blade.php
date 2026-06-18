@extends('layouts.app')

@section('title', 'Peta Interaktif')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Peta Interaktif</h1>
        <p class="text-gray-600">Visualisasi laporan dan progres perbaikan di desa</p>
    </div>

    <!-- Map Container -->
    <div class="bg-white rounded-lg shadow p-4">
        <div class="flex justify-between items-center mb-4 flex-wrap gap-4">
            <div class="flex items-center space-x-4 flex-wrap gap-2">
                <div class="flex items-center">
                    <div class="w-4 h-4 bg-red-500 rounded-full mr-2"></div>
                    <span class="text-sm">Laporan Baru</span>
                </div>
                <div class="flex items-center">
                    <div class="w-4 h-4 bg-yellow-500 rounded-full mr-2"></div>
                    <span class="text-sm">Sedang Dikerjakan</span>
                </div>
                <div class="flex items-center">
                    <div class="w-4 h-4 bg-green-500 rounded-full mr-2"></div>
                    <span class="text-sm">Selesai</span>
                </div>
                <div class="flex items-center">
                    <div class="w-4 h-4 bg-blue-500 rounded-full mr-2"></div>
                    <span class="text-sm">Lokasi Anda</span>
                </div>
                <div class="flex items-center">
                    <div class="w-4 h-4 bg-purple-500 rounded-full mr-2"></div>
                    <span class="text-sm">Semua Laporan</span>
                </div>
            </div>
            <div class="flex space-x-2 flex-wrap gap-2">
                <button onclick="locateUser()" 
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 flex items-center">
                    <i class="fas fa-location-crosshairs mr-2"></i> Lokasi Saya
                </button>
                <button onclick="showAllReports()" 
                        class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 flex items-center">
                    <i class="fas fa-layer-group mr-2"></i> Tampilkan Semua
                </button>
                <button onclick="refreshMap()" 
                        class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 flex items-center">
                    <i class="fas fa-sync-alt mr-2"></i> Refresh
                </button>
                @if(!auth()->user()->isAdmin())
                <a href="{{ route('laporan.create') }}" 
                   class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 flex items-center">
                    <i class="fas fa-plus mr-2"></i> Buat Laporan
                </a>
                @endif
            </div>
        </div>
        
        <div id="map" class="map-container rounded-lg"></div>
        
        <!-- Stats -->
        <div class="mt-4 grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-gray-50 p-4 rounded-lg">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-map-marker-alt text-blue-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Total Laporan</p>
                        <p class="text-2xl font-bold" id="total-reports">0</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-gray-50 p-4 rounded-lg">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-check-circle text-green-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Selesai</p>
                        <p class="text-2xl font-bold" id="completed-reports">0</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-gray-50 p-4 rounded-lg">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-tools text-yellow-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Dikerjakan</p>
                        <p class="text-2xl font-bold" id="progress-reports">0</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-gray-50 p-4 rounded-lg">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-clock text-red-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Menunggu</p>
                        <p class="text-2xl font-bold" id="pending-reports">0</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="mt-4 text-sm text-gray-600">
            <p><i class="fas fa-info-circle mr-2"></i>Klik pada peta untuk membuat laporan baru di lokasi tersebut</p>
            <p class="mt-1"><i class="fas fa-user-location mr-2"></i>Izinkan akses lokasi untuk melihat posisi Anda di peta</p>
            <p class="mt-1"><i class="fas fa-map-marked-alt mr-2"></i>Klik marker laporan untuk melihat detail</p>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let map;
    let markers = [];
    let userMarker = null;
    let userLocation = null;
    let reportLayer = null;
    let reportsData = [];

    // Initialize map with user location
    function initMap() {
        // Default center (adjust to your village coordinates)
        const defaultCenter = [-6.2088, 106.8456];
        
        map = L.map('map').setView(defaultCenter, 14);
        
        // Add OpenStreetMap tile layer
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors',
            maxZoom: 19
        }).addTo(map);
        
        // Try to get user location
        locateUser();
        
        // Add click event for creating reports
        map.on('click', function(e) {
            @if(!auth()->user()->isAdmin())
            const confirmCreate = confirm(`Buat laporan di lokasi ini?\nLat: ${e.latlng.lat.toFixed(6)}\nLng: ${e.latlng.lng.toFixed(6)}`);
            if (confirmCreate) {
                window.location.href = "{{ route('laporan.create') }}" + 
                    "?lat=" + e.latlng.lat + 
                    "&lng=" + e.latlng.lng;
            }
            @endif
        });
        
        // Load reports
        loadReports();
    }
    
    // Get user location
    function locateUser() {
        if (!navigator.geolocation) {
            alert('Browser Anda tidak mendukung geolocation');
            return;
        }
        
        // Show loading
        const locateBtn = document.querySelector('[onclick="locateUser()"]');
        const originalText = locateBtn.innerHTML;
        locateBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Mendeteksi...';
        locateBtn.disabled = true;
        
        navigator.geolocation.getCurrentPosition(
            function(position) {
                userLocation = {
                    lat: position.coords.latitude,
                    lng: position.coords.longitude
                };
                
                // Update map view
                map.setView([userLocation.lat, userLocation.lng], 16);
                
                // Add or update user marker
                if (userMarker) {
                    userMarker.setLatLng([userLocation.lat, userLocation.lng]);
                } else {
                    const userIcon = L.divIcon({
                        html: `<div class="relative">
                                 <div class="w-10 h-10 bg-blue-600 rounded-full border-3 border-white shadow-lg animate-pulse"></div>
                                 <div class="absolute inset-0 flex items-center justify-center">
                                   <i class="fas fa-user text-white text-sm"></i>
                                 </div>
                               </div>`,
                        className: 'user-location-marker',
                        iconSize: [40, 40],
                        iconAnchor: [20, 20]
                    });
                    
                    userMarker = L.marker([userLocation.lat, userLocation.lng], { 
                        icon: userIcon,
                        zIndexOffset: 1000 
                    })
                    .addTo(map)
                    .bindPopup('<strong>📍 Posisi Anda</strong><br>Anda berada di sini')
                    .openPopup();
                }
                
                // Restore button
                locateBtn.innerHTML = originalText;
                locateBtn.disabled = false;
                
            },
            function(error) {
                console.error('Geolocation error:', error);
                alert('Tidak dapat mendapatkan lokasi Anda. Pastikan izin lokasi diaktifkan.');
                
                // Restore button
                locateBtn.innerHTML = originalText;
                locateBtn.disabled = false;
            },
            {
                enableHighAccuracy: true,
                timeout: 10000,
                maximumAge: 0
            }
        );
    }
    
    function loadReports() {
        // Clear existing markers
        markers.forEach(marker => map.removeLayer(marker));
        markers = [];
        
        // Clear existing layer group if exists
        if (reportLayer) {
            map.removeLayer(reportLayer);
        }
        
        // Create new layer group for reports
        reportLayer = L.layerGroup().addTo(map);
        
        // Fetch reports via API
        fetch("{{ route('api.laporan.peta') }}")
            .then(response => response.json())
            .then(data => {
                reportsData = data;
                // Update stats
                updateStats(data);
                
                // Add markers for each report
                data.forEach(report => {
                    const marker = createReportMarker(report);
                    markers.push(marker);
                    marker.addTo(reportLayer);
                });
                
                // Fit bounds to show all markers if there are reports
                if (data.length > 0) {
                    const bounds = L.latLngBounds(data.map(r => [r.lat, r.lng]));
                    if (userLocation) {
                        bounds.extend([userLocation.lat, userLocation.lng]);
                    }
                    map.fitBounds(bounds, { padding: [50, 50] });
                }
            })
            .catch(error => console.error('Error loading reports:', error));
    }
    
    function createReportMarker(report) {
        let iconColor;
        let iconHtml;
        let popupContent;
        
        switch(report.status) {
            case 'diterima':
                iconColor = 'red';
                iconHtml = '<i class="fas fa-exclamation-circle"></i>';
                popupContent = `
                    <div class="p-2 min-w-[200px]">
                        <strong class="text-red-600">${report.judul}</strong>
                        <p class="text-sm text-gray-600 mt-1">Status: <span class="text-red-500">Menunggu Pengerjaan</span></p>
                        <p class="text-sm text-gray-600">Tanggal: ${report.tanggal}</p>
                        <p class="text-sm text-gray-600">Kategori: ${report.kategori}</p>
                        <a href="/laporan/${report.id}" class="inline-block mt-2 px-3 py-1 bg-blue-600 text-white text-xs rounded hover:bg-blue-700">
                            Lihat Detail
                        </a>
                    </div>
                `;
                break;
            case 'dikerjakan':
                iconColor = 'yellow';
                iconHtml = '<i class="fas fa-tools"></i>';
                popupContent = `
                    <div class="p-2 min-w-[200px]">
                        <strong class="text-yellow-600">${report.judul}</strong>
                        <p class="text-sm text-gray-600 mt-1">Status: <span class="text-yellow-500">Sedang Dikerjakan</span></p>
                        <p class="text-sm text-gray-600">Tanggal: ${report.tanggal}</p>
                        <p class="text-sm text-gray-600">Kategori: ${report.kategori}</p>
                        <a href="/laporan/${report.id}" class="inline-block mt-2 px-3 py-1 bg-blue-600 text-white text-xs rounded hover:bg-blue-700">
                            Lihat Detail
                        </a>
                    </div>
                `;
                break;
            case 'selesai':
                iconColor = 'green';
                iconHtml = '<i class="fas fa-check-circle"></i>';
                popupContent = `
                    <div class="p-2 min-w-[200px]">
                        <strong class="text-green-600">${report.judul}</strong>
                        <p class="text-sm text-gray-600 mt-1">Status: <span class="text-green-500">Selesai</span></p>
                        <p class="text-sm text-gray-600">Tanggal: ${report.tanggal}</p>
                        <p class="text-sm text-gray-600">Kategori: ${report.kategori}</p>
                        @if($laporan->biaya)
                        <p class="text-sm text-gray-600 mt-1">Biaya: Rp ${new Intl.NumberFormat('id-ID').format(report.biaya || 0)}</p>
                        @endif
                        <a href="/laporan/${report.id}" class="inline-block mt-2 px-3 py-1 bg-blue-600 text-white text-xs rounded hover:bg-blue-700">
                            Lihat Detail
                        </a>
                    </div>
                `;
                break;
            default:
                iconColor = 'gray';
                iconHtml = '<i class="far fa-circle"></i>';
                popupContent = `
                    <div class="p-2 min-w-[200px]">
                        <strong>${report.judul}</strong>
                        <p class="text-sm text-gray-600">Status: ${report.status}</p>
                        <p class="text-sm text-gray-600">Tanggal: ${report.tanggal}</p>
                    </div>
                `;
        }
        
        const icon = L.divIcon({
            html: `<div style="background-color: ${iconColor};" 
                     class="w-12 h-12 rounded-full border-3 border-white shadow-lg flex items-center justify-center text-white text-lg cursor-pointer hover:scale-110 transition-transform duration-200">
                      ${iconHtml}
                   </div>`,
            className: 'report-marker',
            iconSize: [48, 48],
            iconAnchor: [24, 24]
        });
        
        return L.marker([report.lat, report.lng], { icon })
            .bindPopup(popupContent);
    }
    
    function updateStats(reports) {
        const total = reports.length;
        const completed = reports.filter(r => r.status === 'selesai').length;
        const inProgress = reports.filter(r => r.status === 'dikerjakan').length;
        const pending = reports.filter(r => r.status === 'diterima').length;
        
        document.getElementById('total-reports').textContent = total;
        document.getElementById('completed-reports').textContent = completed;
        document.getElementById('progress-reports').textContent = inProgress;
        document.getElementById('pending-reports').textContent = pending;
    }
    
    function showAllReports() {
        if (markers.length === 0) {
            alert('Tidak ada laporan untuk ditampilkan');
            return;
        }
        
        const bounds = L.latLngBounds(markers.map(m => m.getLatLng()));
        if (userLocation) {
            bounds.extend([userLocation.lat, userLocation.lng]);
        }
        map.fitBounds(bounds, { padding: [50, 50] });
    }
    
    function refreshMap() {
        loadReports();
        if (userLocation) {
            map.setView([userLocation.lat, userLocation.lng], map.getZoom());
        }
    }
    
    // Initialize map when page loads
    document.addEventListener('DOMContentLoaded', initMap);
</script>

<style>
.map-container {
    height: 600px;
    z-index: 1;
}

.report-marker {
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.05); }
    100% { transform: scale(1); }
}

.user-location-marker {
    animation: bounce 1s infinite alternate;
}

@keyframes bounce {
    from { transform: translateY(0); }
    to { transform: translateY(-5px); }
}

.leaflet-popup-content {
    max-width: 250px !important;
}
</style>
@endpush
@endsection