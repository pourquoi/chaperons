import { Component, Input, OnInit, Output, EventEmitter, NgZone } from '@angular/core';

import * as _ from 'lodash';
import * as d3 from './../../d3.bundle';

import { Map } from '../../models/map';
import { Nursery } from '../../models/nursery';

import { GmapLoaderService } from '../../services/gmap-loader';
import { GmapUtils } from '../../services/gmap-utils';
import { gmapStyles } from './../../gmap-styles/gmap-styles';
import { ApiService } from './../../services/api';

import { PointsOverlay } from './points-overlay';

@Component({
  selector: 'map-gmap',
  templateUrl: './map-gmap.html',
  styleUrls: ['./map-gmap.css']
})
export class MapGmapComponent implements OnInit
{
    @Input()
    map: Map;

    @Input()
    mode: string;

    @Input()
    width: number;

    @Input()
    height: number;

    @Output()
    changed = new EventEmitter<boolean>();

    gmap: google.maps.Map;
    viewport: google.maps.Rectangle;

    markers: Array<any> = [];

    styles: string[] = [];

    symbolSpec = {
        strokeColor: '#fff',
        strokeWeight: 1,
        fillOpacity: 0.9,
        strokeOpacity: 1,
        scale: 3
    };

    private compute_bounds = false;

    private overlay_nursery_owned: PointsOverlay;
    private overlay_nursery: PointsOverlay;
    private overlay_family: PointsOverlay;

    private gmapUtils: GmapUtils = new GmapUtils();

    constructor(private gmapLoaderService: GmapLoaderService, private zone: NgZone, private api: ApiService) {
        for (const k in gmapStyles) {
            this.styles.push(k);
        }
    }

    get style() {
        if ( this.map.style_name && this.map.style_name in gmapStyles ) {
            return this.map.style_name;
        } else {
            return 'standard';
        }
    }
    set style(style) {
        this.map.style_name = style;
        this.changed.emit(true);

        if (this.gmap) {
            this.gmap.setOptions({styles: gmapStyles[style]});
        }
    }

    ngOnInit() {
        if (this.width && this.height) {
            document.getElementById('map').style['width'] = this.width + 'px';
            document.getElementById('map').style['height'] = this.height + 'px';
        }

        this.gmapLoaderService.loadAPI.then(() => {

            const el = document.getElementById('map');

            this.gmap = new google.maps.Map(el,
            {
                disableDefaultUI: this.mode === 'render',
                zoomControl: this.mode !== 'render',
                mapTypeControl: false,
                scaleControl: this.mode !== 'render',
                streetViewControl: false,
                rotateControl: false,

                zoom: this.map.zoom || 5,
                center: {lat: 47.101559, lng: 2.281636},
                styles: gmapStyles[this.style]
            });

            if ( !this.map_bounds || !this.map.center_lat ) {
                this.compute_bounds = true;
            } else {
                this.gmap.setCenter({lat: this.map.center_lat, lng: this.map.center_lng});
                this.gmap.setZoom(this.map.zoom || 5);
            }

            // create family and nurseries markers
            setTimeout( () => this.createPoints(), 100 );

            if ( this.mode !== 'render' ) {

                this.gmap.addListener('bounds_changed', () => {
                    this.map.ne_lat = this.gmap.getBounds().getNorthEast().lat();
                    this.map.ne_lng = this.gmap.getBounds().getNorthEast().lng();
                    this.map.sw_lat = this.gmap.getBounds().getSouthWest().lat();
                    this.map.sw_lng = this.gmap.getBounds().getSouthWest().lng();

                    console.log('map bounds changed');
                });

                this.gmap.addListener('center_changed', () => {
                    this.map.center_lat = this.gmap.getCenter().lat();
                    this.map.center_lng = this.gmap.getCenter().lng();
                });

                this.gmap.addListener('zoom_changed', () => {
                    this.map.zoom = this.gmap.getZoom();

                    console.log('zoom_changed');
                });
            }
        });
    }

    createPoints() {
        this.api.getResource('/nurseries?map_id=' + this.map.id, true).subscribe(
            (nurseries) => {

                let bounds = new google.maps.LatLngBounds();

                let points_owned = [];
                let  points = [];

                for (const n of nurseries) {
                    const pos = new google.maps.LatLng(n.address.latitude, n.address.longitude);
                    if (n.nature === 'PARTNER') {
                        points.push({pos});
                    } else {
                        points_owned.push({pos});
                    }
                    // bounds.extend(pos);
                }

                this.overlay_nursery_owned = new PointsOverlay(this.gmap, points_owned, 'overlay-nursery-owned', {
                    fill: this.map.fill_color_nursery_owned || '#e31c18',
                    zIndex: 1002
                });
                this.overlay_nursery_owned.createOverlay();

                this.overlay_nursery = new PointsOverlay(this.gmap, points, 'overlay-nursery', {
                    fill: this.map.fill_color_nursery || '#1855e3',
                    zIndex: 1001
                });
                this.overlay_nursery.createOverlay();

                points = [];

                for (const family of this.map.families) {
                    if ( family.address.geocode_status !== 1 ) {
                        continue;
                    }

                    const pos = new google.maps.LatLng(family.address.latitude, family.address.longitude);

                    points.push({pos});

                    if ( bounds.isEmpty() ) {
                        // create bounds 5km around the first marker
                        const ne = google.maps.geometry.spherical.computeOffset(pos, 5000, 45);
                        const sw = google.maps.geometry.spherical.computeOffset(pos, 5000, 225);
                        bounds = new google.maps.LatLngBounds(sw, ne);
                    }

                    bounds.extend(pos);
                }

                this.overlay_family = new PointsOverlay(this.gmap, points, 'overlay-family', {
                    fill: this.map.fill_color_family || '#44e318',
                    zIndex: 1002,
                    shape: 'square'
                });
                this.overlay_family.createOverlay();

                if ( this.compute_bounds ) {
                    this.map_bounds = bounds;
                    const center = this.map_bounds.getCenter();
                    this.gmap.fitBounds(this.map_bounds);
                }

                // in render mode
                // add a flag for phantomjs to render the page
                setTimeout(() => {
                    window['map_render_ready'] = true;
                }, 5000);
            },
            (err) => console.log(err)
        );
/*
        for(let family of this.map.families) {
            if( family.address.geocode_status != 1 ) continue;
            let m = new google.maps.Marker({
                map: this.gmap,
                position: {lat: family.address.latitude, lng: family.address.longitude},
                icon: _.assign(this.symbolSpec, {
                    path: google.maps.SymbolPath.CIRCLE,
                    fillColor: this.map.fill_color_family || '#e31c18'
                })
            });

            this.markers.push({type:'family', marker:m});

            // keep track of nurseries id to ignore duplicates
            let nursery_ids = [];

            for( let nursery_selection of family.nurseries) {
                let nursery = nursery_selection.nursery;
                if( nursery_ids.indexOf(nursery.id) != -1 ) continue;
                nursery_ids.push(nursery.id);
                let m = new google.maps.Marker({
                    map: this.gmap,
                    position: {lat: nursery.address.latitude, lng: nursery.address.longitude},
                    icon: _.assign(this.symbolSpec, {
                        path:google.maps.SymbolPath.CIRCLE,
                        fillColor: this.map.fill_color_nursery || '#f1cb05'
                    })
                });

                this.markers.push({type:'nursery', marker:m});
            }
        }
*/
    }

    get map_bounds() {
        if (!this.map.ne_lat) {
            return undefined;
        }

        return new google.maps.LatLngBounds(
            new google.maps.LatLng(this.map.sw_lat, this.map.sw_lng),
            new google.maps.LatLng(this.map.ne_lat, this.map.ne_lng)
            );
    }

    set map_bounds(bounds: google.maps.LatLngBounds) {
        if ( bounds ) {
            this.map.ne_lat = bounds.getNorthEast().lat();
            this.map.ne_lng = bounds.getNorthEast().lng();
            this.map.sw_lat = bounds.getSouthWest().lat();
            this.map.sw_lng = bounds.getSouthWest().lng();
        } else {
            this.map.ne_lat = this.map.ne_lng = this.map.sw_lat = this.map.sw_lng = undefined;
        }
    }

    updateColors($event, type) {
        this.map['fill_color_' + type] = $event;

        if (('overlay_' + type) in this) {
            this['overlay_' + type].updateOptions({
                fill: $event
            });
        }

        d3.select('#map').selectAll('#overlay-' + type + '.points-layer circle')
            .style('fill', $event);
        d3.select('#map').selectAll('#overlay-' + type + '.points-layer rect')
            .style('fill', $event);

/*

        for(let m of this.markers) {
            if(m.type != type) continue;
            let marker:google.maps.Marker = m.marker;
            marker.setIcon( _.assign(this.symbolSpec, {
                path:google.maps.SymbolPath.CIRCLE,
                fillColor: this.map['fill_color_'+type]
                })
            );
        }
        */
        this.changed.emit(true);
    }
}
