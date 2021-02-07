import * as d3 from './../../d3.bundle';
import * as _ from 'lodash';

export class PointsOverlay {
    private overlay;
    private layer;
    private options;

    constructor(private map, private points, private id, options = {}) {
        this.options = _.assign({
           fill: 'red',
           stroke: 'white',
           zIndex: 1001,
           shape: 'circle'
        }, options);
    }

    updateOptions(options = {}) {
        this.options = _.assign(this.options, options);
    }

    createOverlay() {
        this.overlay = new google.maps.OverlayView();

        this.overlay.onAdd = () => {
            console.log('add overlay');
            this.onAdd();
        }

        this.overlay.draw = () => {
            console.log('draw overlay');
            this.draw();
        }

        this.overlay.setMap(this.map);

        console.log('overlay added to map');
    }

    onAdd() {
        const panes = this.overlay.getPanes();
        this.layer = d3.select(panes.overlayLayer)
            .style('zIndex', this.options['zIndex'])
            .append('div')
            .attr('id', this.id)
            .style('zIndex', this.options['zIndex'])
            .attr('class', 'points-layer');
    }

    draw() {
        
        const projection = this.overlay.getProjection();
        const padding = 10;

        const transform = function(d) {
            const pos = projection.fromLatLngToDivPixel(d.pos);
            return d3.select(this)
                .style('left', (pos.x - padding) + 'px')
                .style('top', (pos.y - padding) + 'px');
        };

        console.log(this.layer);

        const marker = this.layer.selectAll('svg')
            .data(this.points)
            .each(transform)
            .enter().append('svg')
              .each(transform)
              .attr('class', 'marker');

        if (this.options.shape === 'square') {
            marker.append('rect')
                .attr('x', 0)
                .attr('y', 0)
                .attr('width', 10)
                .attr('height', 10)
                .style('fill', this.options.fill)
                .style('stroke', this.options.stroke);
        } else {
            marker.append('circle')
                .attr('r', 4.5)
                .attr('cx', padding)
                .attr('cy', padding)
                .style('fill', this.options.fill)
                .style('stroke', this.options.stroke);
        }
    }
}
