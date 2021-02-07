import { Component, OnInit } from '@angular/core';
import { Router, ActivatedRoute } from '@angular/router';

import * as _ from "lodash";

import { environment } from '../../../environments/environment';

import { GmapLoaderService } from '../../services/gmap-loader';
import { ApiService } from '../../services/api';

import { User } from '../../models/user';
import { Map } from '../../models/map';
import { Nursery } from '../../models/nursery';

@Component({
  templateUrl: './map-detail.html',
  styleUrls: ['./map-detail.css']
})
export class MapDetailComponent implements OnInit {

  map: Map;
  user: User;

  export_in_progress = false;
  show_remove_confirm = false;

  constructor(
      private route: ActivatedRoute,
      private router: Router,
      private apiService: ApiService,
      private gmapLoaderService: GmapLoaderService
    ) {
    route.data.forEach(
      (data: any) => {
        this.user = data.user;
        this.map = data.map;
      }
    );
  }

  ngOnInit() {
  }

  get export_data_url() {
    return this.apiService.endpoint + '/families/' + this.map.id + '/export?auth-token=' + encodeURIComponent(this.apiService.apiKey);
  }

  get export_stats_url() {
    return this.apiService.endpoint + '/families/' + this.map.id + '/histogram?auth-token=' + encodeURIComponent(this.apiService.apiKey);
  }

  get export_url() {
    if (this.map.capture_filename) {
      return environment.uploadBaseUrl + '/maps/' + this.map.capture_filename;
    } else {
      return undefined;
    }
  }

  export(event) {
    event.preventDefault();
    if ( this.export_in_progress ) {
        return;
    }

    this.export_in_progress = true;

    const obs1 = this._getSaveObserver();
    const obs2 = this.apiService.postResource('/maps/' + this.map.id + '/render', {}, 'PATCH');

    obs1.concat(obs2).subscribe(
      (map) => {
        this.map = map;
      },
      (err) => {
        this.export_in_progress = false;
      },
      () => {
        this.export_in_progress = false;
      }
    );
  }

  toggleRemoveConfirm($event) {
    $event.preventDefault();
    this.show_remove_confirm = !this.show_remove_confirm;
  }

  remove($event) {
    $event.preventDefault();
    this.apiService.deleteResource('/maps/' + this.map.id).subscribe(
      () => this.router.navigate(['maps'])
    );
  }

  _getSaveObserver() {
    this.map.width = document.getElementById('map').clientWidth;
    this.map.height = document.getElementById('map').clientHeight;

    const data = {
      map_style: _.pick(this.map, [
        'fill_color_nursery', 
        'fill_color_nursery_owned',
        'fill_color_family', 
        'style_name',
        'ne_lat', 'ne_lng', 'sw_lat', 'sw_lng',
        'zoom',
        'center_lat', 'center_lng',
        'height', 'width'
        ])
    }
    return this.apiService.postResource('/maps/' + this.map.id + '/style', data, 'PATCH');
  }

  save() {
    this._getSaveObserver().subscribe();
  }
}
