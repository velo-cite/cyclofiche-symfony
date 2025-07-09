export class NewPointManager {
    constructor(map) {

        const canvas = map.getCanvasContainer();

        const geojson = {
            'type': 'FeatureCollection',
            'features': []
        };

        function onMove(e) {
            const coords = e.lngLat;

            // Set a UI indicator for dragging.
            canvas.style.cursor = 'grabbing';

            // Update the Point feature in `geojson` coordinates
            // and call setData to the source layer `point` on it.
            geojson.features[0].geometry.coordinates = [coords.lng, coords.lat];
            map.getSource('point').setData(geojson);
        }

        function onUp(e) {
            // Unbind mouse/touch events
            map.off('mousemove', onMove);
            map.off('touchmove', onMove);
        }

        map.on('load', () => {
            // Add a single point to the map
            map.addSource('point', {
                'type': 'geojson',
                'data': geojson
            });

            map.addLayer({
                'id': 'point',
                'type': 'circle',
                'source': 'point',
                'paint': {
                    'circle-radius': 10,
                    'circle-color': '#3887be'
                }
            });

            // When the cursor enters a feature in the point layer, prepare for dragging.
            map.on('mouseenter', 'point', () => {
                map.setPaintProperty('point', 'circle-color', '#3bb2d0');
                canvas.style.cursor = 'move';
            });

            map.on('mouseleave', 'point', () => {
                map.setPaintProperty('point', 'circle-color', '#3887be');
                canvas.style.cursor = '';
            });

            map.on('mousedown', 'point', (e) => {
                // Prevent the default map drag behavior.
                e.preventDefault();

                canvas.style.cursor = 'grab';

                map.on('mousemove', onMove);
                map.once('mouseup', onUp);
            });

            map.on('touchstart', 'point', (e) => {
                if (e.points.length !== 1) return;

                // Prevent the default map drag behavior.
                e.preventDefault();

                map.on('touchmove', onMove);
                map.once('touchend', onUp);
            });
        });
    }
}