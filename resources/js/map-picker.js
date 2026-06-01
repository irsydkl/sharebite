import L from 'leaflet';
import iconRetina from 'leaflet/dist/images/marker-icon-2x.png';
import icon from 'leaflet/dist/images/marker-icon.png';
import shadow from 'leaflet/dist/images/marker-shadow.png';
import 'leaflet/dist/leaflet.css';

delete L.Icon.Default.prototype._getIconUrl;

L.Icon.Default.mergeOptions({
    iconRetinaUrl: iconRetina,
    iconUrl: icon,
    shadowUrl: shadow,
});

const DEFAULT_CENTER = [-6.2088, 106.8456];
const DEFAULT_ZOOM = 13;

function hasCoordinates(lat, lng) {
    return lat !== '' && lng !== '' && ! Number.isNaN(Number(lat)) && ! Number.isNaN(Number(lng));
}

function initMapPicker(container) {
    const latInput = document.getElementById(container.dataset.latInput);
    const lngInput = document.getElementById(container.dataset.lngInput);
    const coordsDisplay = container.querySelector('[data-coords-display]');
    const required = container.dataset.required === '1';
    const mapElement = container.querySelector('[data-map]');

    if (! latInput || ! lngInput || ! mapElement) {
        return;
    }

    const initialLat = container.dataset.initialLat;
    const initialLng = container.dataset.initialLng;
    const hasInitial = hasCoordinates(initialLat, initialLng);

    const map = L.map(mapElement).setView(
        hasInitial ? [Number(initialLat), Number(initialLng)] : DEFAULT_CENTER,
        DEFAULT_ZOOM,
    );

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
        maxZoom: 19,
    }).addTo(map);

    let marker = null;

    const updateInputs = (lat, lng) => {
        latInput.value = lat.toFixed(7);
        lngInput.value = lng.toFixed(7);

        if (coordsDisplay) {
            coordsDisplay.textContent = `${latInput.value}, ${lngInput.value}`;
        }
    };

    const placeMarker = (lat, lng) => {
        if (marker) {
            marker.setLatLng([lat, lng]);
        } else {
            marker = L.marker([lat, lng], { draggable: true }).addTo(map);

            marker.on('dragend', () => {
                const { lat: markerLat, lng: markerLng } = marker.getLatLng();
                updateInputs(markerLat, markerLng);
            });
        }

        updateInputs(lat, lng);
    };

    map.on('click', (event) => {
        placeMarker(event.latlng.lat, event.latlng.lng);
    });

    if (hasInitial) {
        placeMarker(Number(initialLat), Number(initialLng));
    }

    const locateButton = container.querySelector('[data-locate-me]');

    if (locateButton) {
        locateButton.addEventListener('click', () => {
            if (! navigator.geolocation) {
                window.alert('Browser Anda tidak mendukung geolokasi.');

                return;
            }

            locateButton.disabled = true;
            locateButton.textContent = 'Mencari lokasi...';

            navigator.geolocation.getCurrentPosition(
                (position) => {
                    const { latitude, longitude } = position.coords;
                    map.setView([latitude, longitude], 16);
                    placeMarker(latitude, longitude);
                    locateButton.disabled = false;
                    locateButton.textContent = 'Gunakan lokasi saya';
                },
                () => {
                    window.alert('Tidak dapat mengambil lokasi. Izinkan akses GPS atau pilih titik di peta.');
                    locateButton.disabled = false;
                    locateButton.textContent = 'Gunakan lokasi saya';
                },
                { enableHighAccuracy: true, timeout: 10000 },
            );
        });
    }

    const form = container.closest('form');

    if (form) {
        form.addEventListener('submit', (event) => {
            if (required && ! hasCoordinates(latInput.value, lngInput.value)) {
                event.preventDefault();
                window.alert('Silakan tentukan lokasi dengan mengetuk peta atau menggunakan tombol lokasi saya.');
            }
        });
    }

    setTimeout(() => map.invalidateSize(), 100);
}

document.querySelectorAll('[data-map-picker]').forEach(initMapPicker);
