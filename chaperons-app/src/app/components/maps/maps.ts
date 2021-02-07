import { Component, OnInit } from '@angular/core';

import * as moment from 'moment';
import 'moment/locale/fr';

import { environment } from '../../../environments/environment';

import { ApiService } from '../../services/api';
import { AuthService } from '../../services/auth';

import { Map } from '../../models/map';

@Component({
  templateUrl: './maps.html',
  styleUrls: ['./maps.scss']
})
export class MapsComponent implements OnInit {

  maps = [];

  orders = {
    'recent': 'les plus récentes',
    'old': 'les plus anciennes'
  };

  start = 0;
  limit = 20;
  has_next_page = false;

  order = 'recent';

  constructor(private apiService: ApiService, private authService: AuthService) {
    moment.locale('fr');
  }

  ngOnInit() {
    this.getMaps();
  }

  getMaps() {
    this.maps = [];
    const url = '/users/' + this.authService.user.id + '/maps?order=' + encodeURIComponent(this.order) +
      '&start=' + this.start + '&limit=' + (this.limit + 1);
    this.apiService.getResource(url).subscribe(
      maps => {
        if ( maps.length > this.limit ) {
          this.has_next_page = true;
          maps.pop();
        } else {
          this.has_next_page = false;
        }
        this.maps = maps;
      },
      err => console.log(err)
    );
  }

  nextPage($event) {
    $event.preventDefault();
    this.start = this.start + this.limit;
    this.getMaps();
  }

  previousPage($event) {
    $event.preventDefault();
    this.start = Math.max(0, this.start - this.limit);
    this.getMaps();
  }

  changeOrder(order) {
    this.order = order;
    this.getMaps();
    return false;
  }

  thumbnail(map: Map) {
    if (map.capture_filename) {
      return 'url(' + environment.uploadBaseUrl + '/maps/' + map.capture_filename + ')';
    }
    return 'url(/assets/img/map-thumbnail.png)';
  }

  dateText(map: Map) {
    return moment(map.created_at).fromNow();
  }

}
