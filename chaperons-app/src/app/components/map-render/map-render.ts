import { Component, OnInit } from '@angular/core';
import { ActivatedRoute } from '@angular/router';

import { GmapLoaderService } from '../../services/gmap-loader';

import { Map } from '../../models/map';

@Component({
  templateUrl: './map-render.html',
  styleUrls: ['./map-render.css']
})
export class MapRenderComponent implements OnInit {

  map: Map;

  public width: number;
  public height: number;

  constructor(private route: ActivatedRoute, private gmapLoaderService: GmapLoaderService) {
    route.data.forEach(
      (data: any) => {
        this.map = data.map;
      }
    );

    this.width = this.map.width || 430;
    this.height = this.map.height || 740;
  }

  ngOnInit() {

  }

}
