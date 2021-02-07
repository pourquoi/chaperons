import { Component, AfterViewInit, Input, ElementRef } from '@angular/core';

import * as d3 from '../../d3.bundle';

import { MapStats } from '../../services/map-stats';
import { Map } from '../../models/map';

@Component({
    selector: 'chaperons-map-stats',
    template: `
    <div class="map-stats-header">
        <div class="map-stats-label">Au moins une crèche à moins de</div>
        <div class="map-stats-value">% (Propres)</div>
        <div class="map-stats-value">% (Partenaires)</div>
        <div class="map-stats-value">% (Total)</div>
    </div>
    `
})
export class MapStatsTableComponent implements AfterViewInit
{
    @Input()
    map: Map;

    host;

    constructor(el: ElementRef) {
        this.host = d3.select(el.nativeElement);
    }

    ngAfterViewInit() {
        this.render();
    }

    render() {
        const statsService = new MapStats(this.map);
        const data = statsService.getDistanceHistogram(true, true, ['families', 'families_owned', 'families_partner']);

        const items = this.host.selectAll('.map-stats-item')
            .data(data)
            .enter()
            .append('div')
            .attr('class', (d, i) => i % 2 ? 'map-stats-item' : 'map-stats-item alt');

        items.append('div')
            .attr('class', 'map-stats-label')
            .text((d) => d.distance_label);

        items.append('div')
            .attr('class', 'map-stats-value')
            .text((d) => (d.families_owned * 100).toFixed(1) + '%');

        items.append('div')
            .attr('class', 'map-stats-value')
            .text((d) => (d.families_partner * 100).toFixed(1) + '%');

        items.append('div')
            .attr('class', 'map-stats-value')
            .text((d) => (d.families * 100).toFixed(1) + '%');

    }
}
