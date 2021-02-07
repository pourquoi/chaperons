import { Map } from '../models/map';

import * as _ from "lodash";

export class MapStats {
    constructor(private map: Map) {
    }

    getDistanceHistogram(cumulative: boolean = false, ratio: boolean = false, keys: Array<string> = null) {
        keys = keys ?? ['families'];

        const distances = [250, 500, 750, 1000, 1500, 2000, 2500, 3000, 4000, 5000, 10000, 20000, 30000];
        const labels = distances.map((d) => {
            if ( d < 1000 ) {
                return d.toString() + ' m';
            }
            d = d / 1000;
            if ( d !== Math.round(d) ) {
                return d.toFixed(1) + ' km';
            } else {
                return d.toFixed(0) + ' km';
            }
        });

        const hist = [];

        let i = 0;

        while ( i < distances.length ) {
            hist[i] = {
                distance: distances[i],
                families: 0,
                families_owned: 0,
                families_partner: 0,
                distance_label: labels[i]
            };
            i++;
        }

        if (keys.includes('families')) {
            this._fillHist(hist, ratio, cumulative, 'families');
        }

        if (keys.includes('families_owned')) {
            this._fillHist(hist, ratio, cumulative, 'families_owned',
                (sel) => sel.nursery.nature !== 'PARTNER');
        }
        if (keys.includes('families_partner')) {
            this._fillHist(hist, ratio, cumulative, 'families_partner',
                (sel) => sel.nursery.nature === 'PARTNER');
        }

        return hist;
    }

    _fillHist(hist, ratio = false, cumulative = false, key = 'families', filterSel = null) {
        let total = 0;

        let i = 0;

        for (const family of this.map.families) {
            const nurseries = filterSel ? family.nurseries.filter(filterSel) : family.nurseries;

            const distance = _.min(_.map(nurseries, 'distance'));
            i = 0;
            if ( distance ) {
                total++;
                while (i < hist.length) {
                    if ( distance < hist[i].distance ) {
                        hist[i][key]++;
                        if ( !cumulative ) {
                            break;
                        }
                    }
                    i++;
                }
            }
        }

        if ( ratio && total ) {
            hist.map((h) => {
                h[key] /= total;
                return h;
            });
        }
    }
}