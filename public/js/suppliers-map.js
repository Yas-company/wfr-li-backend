class SuppliersMap {
    constructor() {
        this.map = null;
        this.markers = [];
        this.suppliers = [
            {
                id: 1,
                name: 'مورد المنطقة الشمالية',
                description: 'متخصص في منتجات الألبان والمنتجات الطازجة',
                location: [26.4207, 50.0882], // Dammam coordinates
                icon: '🥛'
            },
            {
                id: 2,
                name: 'مورد المنطقة الشرقية',
                description: 'متخصص في الحبوب والأرز والبقوليات',
                location: [24.7136, 46.6753], // Riyadh coordinates
                icon: '🌾'
            },
            {
                id: 3,
                name: 'مورد المنطقة الغربية',
                description: 'متخصص في الأغذية المصنعة والمشروبات',
                location: [21.4858, 39.1925], // Jeddah coordinates
                icon: '🥤'
            },
            {
                id: 4,
                name: 'مورد المنطقة الجنوبية',
                description: 'متخصص في اللحوم والدواجن والمأكولات البحرية',
                location: [18.2208, 42.5051], // Abha coordinates
                icon: '🥩'
            }
        ];
        this.init();
    }

    init() {
        // Initialize map
        this.map = L.map('suppliers-map').setView([24.7136, 46.6753], 6);

        // Add tile layer (OpenStreetMap)
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(this.map);

        // Add markers for each supplier
        this.suppliers.forEach(supplier => {
            const marker = L.marker(supplier.location, {
                icon: L.divIcon({
                    className: 'custom-div-icon',
                    html: `<div class="marker-pin">${supplier.icon}</div>`,
                    iconSize: [30, 42],
                    iconAnchor: [15, 42]
                })
            });

            // Add popup with supplier information
            marker.bindPopup(`
                <div class="text-right">
                    <h3 class="font-bold text-lg mb-1">${supplier.name}</h3>
                    <p class="text-gray-600">${supplier.description}</p>
                </div>
            `);

            marker.addTo(this.map);
            this.markers.push(marker);
        });

        // Add custom styles for markers
        this.addCustomStyles();

        // Handle map visibility
        this.handleMapVisibility();
    }

    addCustomStyles() {
        const style = document.createElement('style');
        style.textContent = `
            .custom-div-icon {
                background: none;
                border: none;
            }
            .marker-pin {
                width: 30px;
                height: 30px;
                border-radius: 50% 50% 50% 0;
                background: #008080;
                position: absolute;
                transform: rotate(-45deg);
                left: 50%;
                top: 50%;
                margin: -15px 0 0 -15px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 20px;
                color: white;
                box-shadow: 0 2px 4px rgba(0,0,0,0.3);
            }
            .marker-pin::after {
                content: '';
                width: 14px;
                height: 14px;
                margin: 8px 0 0 8px;
                background: #fff;
                position: absolute;
                border-radius: 50%;
            }
            .leaflet-popup-content-wrapper {
                border-radius: 8px;
            }
        `;
        document.head.appendChild(style);
    }

    handleMapVisibility() {
        // Create an Intersection Observer
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    // When map becomes visible, invalidate size and refresh
                    setTimeout(() => {
                        this.map.invalidateSize();
                    }, 100);
                }
            });
        }, { threshold: 0.1 });

        // Observe the map container
        const mapContainer = document.getElementById('suppliers-map');
        if (mapContainer) {
            observer.observe(mapContainer);
        }
    }
}

// Initialize map when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    window.suppliersMap = new SuppliersMap();
}); 