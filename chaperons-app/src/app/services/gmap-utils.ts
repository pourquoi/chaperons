const TILE_SIZE = {height: 256, width: 256};
const ZOOM_MAX = 21;

export class GmapUtils {
    computeBounds(markers: google.maps.Marker[]): google.maps.LatLngBounds {
        let bounds: google.maps.LatLngBounds;

        for ( const marker of markers) {
            const p: google.maps.LatLng = marker.getPosition();
            if (!bounds) {
                // create bounds 5km around the first marker
                const ne = google.maps.geometry.spherical.computeOffset(p, 5000, 45);
                const sw = google.maps.geometry.spherical.computeOffset(p, 5000, 225);
                bounds = new google.maps.LatLngBounds(sw, ne);
            } else {
                bounds = bounds.extend(marker.getPosition());
            }
        }

        return bounds;
    }

    offsetLatLng (gmap: google.maps.Map, latlng, offsetX, offsetY): google.maps.LatLng {
        offsetX = offsetX || 0;
        offsetY = offsetY || 0;
        const scale = Math.pow(2, gmap.getZoom());
        const point = gmap.getProjection().fromLatLngToPoint(latlng);
        const pixelOffset = new google.maps.Point((offsetX/scale), (offsetY/scale));
        const newPoint = new google.maps.Point(
            point.x - pixelOffset.x,
            point.y + pixelOffset.y
        );
        return gmap.getProjection().fromPointToLatLng(newPoint);
    }

    addBoundsMargin(gmap: google.maps.Map, bounds: google.maps.LatLngBounds, margin): google.maps.LatLngBounds {
        const ne = this.offsetLatLng(gmap, bounds.getNorthEast(), margin.right, margin.top);
        const sw = this.offsetLatLng(gmap, bounds.getSouthWest(), margin.left, margin.bottom);

        return new google.maps.LatLngBounds(sw, ne);
    }

    getBoundsZoomLevel (bounds, dimensions): number {
        const latRadian = lat => {
            const sin = Math.sin(lat * Math.PI / 180);
            const radX2 = Math.log((1 + sin) / (1 - sin)) / 2;
            return Math.max(Math.min(radX2, Math.PI), -Math.PI) / 2;
        };
        const zoom = (mapPx, worldPx, fraction) => {
            return Math.floor(Math.log(mapPx / worldPx / fraction) / Math.LN2);
        };
        const ne = bounds.getNorthEast();
        const sw = bounds.getSouthWest();
        const latFraction = (latRadian(ne.lat()) - latRadian(sw.lat())) / Math.PI;
        const lngDiff = ne.lng() - sw.lng();
        const lngFraction = ((lngDiff < 0) ? (lngDiff + 360) : lngDiff) / 360;
        const latZoom = zoom(dimensions.height, TILE_SIZE.height, latFraction);
        const lngZoom = zoom(dimensions.width, TILE_SIZE.width, lngFraction);
        return Math.min(latZoom, lngZoom, ZOOM_MAX);
    }
}
