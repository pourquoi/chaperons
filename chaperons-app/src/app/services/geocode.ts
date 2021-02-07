import { Injectable } from '@angular/core';
import { Observable } from 'rxjs/Observable';
import { HttpClient, HttpHeaders, HttpResponse } from '@angular/common/http';
import '../rxjs.operators';

import { Family } from '../models/family';

@Injectable()
export class GeocodeService {
    constructor(private http: HttpClient) {}

    familyGeocodeRequired(family: Family): boolean {
        return (family.address.geocode_status !== 1);
    }

    geocodeFamilies(families: Family[]): Observable<any> {
        let source: Observable<any>;

        for (const family of families) {
            if (!this.familyGeocodeRequired(family)) {
                continue;
            }
            if (!source) {
                source = this.geocodeFamily(family);
            } else {
                source = source.concat(this.geocodeFamily(family))
            }
        }

        return source;
    }

    geocodeFamily(family: Family, provider = 'google'): Observable<any> {

        const source: Observable<any> = Observable.create(observer => {

            const address = family.address.street + ' ' + family.address.city + ' ' + family.address.zip;

            if (provider === 'google') {

            const geocoder: google.maps.Geocoder = new google.maps.Geocoder();

            geocoder.geocode({address}, (results, status) => {
                if (status === google.maps.GeocoderStatus.OK ) {
                    const loc = results[0].geometry.location;
                    family.address.latitude = loc.lat();
                    family.address.longitude = loc.lng();
                    family.address.geocode_status = 1;
                    console.log('geocoding success', family.address);
                    observer.next(family);

                } else if (status === google.maps.GeocoderStatus.ZERO_RESULTS ){
                    family.address.geocode_status = -1;
                    console.log('geocoding failed', family.address);
                    observer.next(family);

                } else {
                    console.log('geocoding error', status);
                    observer.error(status);
                }

                observer.complete();
            });

            // not working (no cross origin headers)
            } else if (provider === 'data.gouv.fr') {
                const q_addr: string = encodeURIComponent(family.address.street + ' ' + family.address.city);
                this.http.get('http://adresse.data.gouv.fr/search/?q=' + q_addr + '&code=' + family.address.zip).subscribe(
                    (res) => {
                        if (res['features'].length) {
                            family.address.latitude = res['features'][0]['geometry']['coordinates'][1];
                            family.address.longitude = res['features'][0]['geometry']['coordinates'][0];
                            family.address.geocode_status = 1;
                            console.log('geocoding success', family.address);
                            observer.next(family);
                        } else {
                            family.address.geocode_status = -1;
                            console.log('geocoding failed', family.address);
                            observer.next(family);
                        }
                    },
                    (err) => {
                        family.address.geocode_status = -1;
                        console.log('geocoding failed', family.address);
                        observer.next(family);
                    });
            }
        });

        return source;
    }

}
