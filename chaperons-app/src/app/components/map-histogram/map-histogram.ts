import { Component, Input, AfterViewInit, ElementRef } from '@angular/core';

import * as d3 from '../../d3.bundle';
import * as _ from 'lodash';

import { MapStats } from '../../services/map-stats';
import { Map } from '../../models/map';

@Component({
  selector: 'chaperons-histogram',
  template: ``,
  styleUrls: ['./map-histogram.css']
})
export class MapHistogramComponent implements AfterViewInit
{
    @Input()
    map: Map;

    host;

    margins = {top: 20, right: 20, bottom: 30, left: 40};

    constructor(el: ElementRef) {
        this.host = d3.select(el.nativeElement);
    }

    ngAfterViewInit() {
        this.render();
    }

    render() {
        const statsService = new MapStats(this.map);
        const data = statsService.getDistanceHistogram();

        let width = this.host.node().clientWidth;
        let height = this.host.node().clientHeight;

        console.log(width, height);

        const margin = {top: 20, right: 20, bottom: 30, left: 0};

        const svg = this.host.append('svg')
            .attr('width', width)
            .attr('height', height);

        width = width - margin.left - margin.right;
        height = height - margin.top - margin.bottom;

        const x = d3.scaleBand().range([0, width]).padding(0.1);
        const y = d3.scaleLinear().range([height, 0]);

        const g = svg.append('g')
            .attr('transform', 'translate(' + margin.left + ',' + margin.top + ')');

        x.domain(data.map((d) => d.distance_label));
        y.domain([0, _.max(data.map((d) => d.families))]);

        g.append('g')
            .attr('class', 'axis axis--x')
            .attr('transform', 'translate(0,' + height + ')')
            .call(d3.axisBottom(x).tickSizeInner(0).tickSizeOuter(0));

        const bar = g.selectAll('.bar')
        .data(_.filter(data, (d) => d.families > 0))
        .enter().append('g')
            .attr('class', 'bar')
            .attr('transform', function(d) { return 'translate(' + x(d.distance_label) + ',' + y(d.families) + ')'; });

        bar.append('rect')
            .attr('x', 1)
            .attr('width', x.bandwidth())
            .attr('height', function(d) { return height - y(d.families); });

        bar.append('text')
            .attr('dy', '.75em')
            .attr('y', 6)
            .attr('x', x.bandwidth() / 2)
            .attr('text-anchor', 'middle')
            .text(function(d) { return d.families; });
    }
}
