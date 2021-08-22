import { Component, OnInit, Input, NgZone } from '@angular/core';

import { Location } from '@angular/common';

import { ActivatedRoute } from '@angular/router';

import * as _ from 'lodash';

import { FileUploader } from 'ng2-file-upload';

import { GmapLoaderService } from '../../services/gmap-loader';
import { GeocodeService } from '../../services/geocode';

import { ApiService } from '../../services/api';

import { Map } from '../../models/map';
import { User } from '../../models/user';
import { Family } from '../../models/family';


@Component({
  templateUrl: './map-edit.html',
  styleUrls: ['./map-edit.css'],
  providers: [
    GeocodeService
  ]
})
export class MapEditComponent implements OnInit {

  map: Map;
  user: User;

  uploader: FileUploader;

  addressErrors = [];

  _show_errors = false;

  saving = false;
  uploading = false;
  geocoding_in_progress = false;

  upload_error = '';
  save_error = '';

  edition_step = 'form';

  stats = {
      micro: 0,
      mac: 0,
      dsp: 0,
      dspc: 0,
      partner: 0,
      other: 0
  };

  _show_name_input = false;

  get show_name_input(): boolean { return this._show_name_input; }
  set show_name_input(show) {
    this._show_name_input = show;
    if (show) {
      setTimeout( () => document.getElementById('name_input_container').focus(), 10 );
    }
  }

  get n_geocoding_required(): number {
    return _.reduce(this.map.families, (sum, o) => sum + ((o.address.geocode_status !== 1) ? 1 : 0), 0);
  }

  get n_address_geocoded(): number {
    return _.reduce(this.map.families, (sum, o) => sum + ((o.address.geocode_status === 1) ? 1 : 0), 0);
  }

  constructor(
      private route: ActivatedRoute,
      private location: Location,
      private apiService: ApiService,
      private gmapLoader: GmapLoaderService,
      private geocoder: GeocodeService,
      private zone: NgZone
    ) {

    route.data.forEach(
      (data: any) => {
        this.user = data.user;
        if ( data.map ) {
          this.map = data.map;
        } else {
          this.map = new Map();
        }
      }
    );

    this.uploader = new FileUploader({
      headers: [{name: 'X-AUTH-TOKEN', value: this.user.api_key}]
    });
    // use lambda callbacks to keep 'this'
    this.uploader.onAfterAddingFile = (fileItem) => {
      return this.onAfterAddingFile(fileItem);
    };
    this.uploader.onCompleteItem = (item, response, status, headers) => {
      return this.onCompleteItem(item, response, status, headers);
    };
  }

  ngOnInit(): void {
    this.apiService.getResource('/nurseries/stats').subscribe(
      (stats) => {
        this.stats.micro = _.reduce(
            stats,
            (sum, o) => {
                if (o['type'] === 'MICRO' && o['nature'] === 'CEP') {
                    return sum + +o['c'];
                } else {
                    return sum;
                }
            },
            0
        );
        
        this.stats.mac = _.reduce(
            stats,
            (sum, o) => {
                if (o['type'] === 'MAC' && o['nature'] === 'CEP') {
                    return sum + +o['c'];
                } else {
                    return sum;
                }
            },
            0
        );

        this.stats.dsp = _.reduce(
            stats,
            (sum, o) => {
                if (o['nature'] === 'DSP') {
                    return sum + +o['c'];
                } else {
                    return sum;
                }
            },
            0
        );

        this.stats.dspc = _.reduce(
            stats,
            (sum, o) => {
                if (o['nature'] === 'DSPC') {
                    return sum + +o['c'];
                } else {
                    return sum;
                }
            },
            0
        );

        this.stats.other = _.reduce(
            stats,
            (sum, o) => {
                const nature = o['nature'] || null;
                const type = o['type'] || null;
                if (
                    ['DSP', 'DSPC', 'PARTNER'].indexOf(nature) === -1
                && (nature === 'CEP' && type !== 'MAC' && type !== 'MICRO')) {
                    return sum + +o['c'];
                } else {
                    return sum;
                }
            },
            0
        );

        this.stats.partner = _.reduce(
            stats,
            (sum, o) => {
                if (o['nature'] === 'PARTNER') {
                    return sum + +o['c'];
                } else {
                    return sum;
                }
            },
            0
        );
      }
    );
  }

  // handle the file upload response and update the family list on success
  onCompleteItem(item, response, status, headers) {
    this.uploading = false;
    if ( status === 200 || status === 201 ) {
      this.upload_error = '';
      this.map = JSON.parse(response);
    } else {
      // @todo parse error
      this.upload_error = 'Echec de l\'import';
      console.log('error uploading file', response);
    }
  }

  // on file selection
  onAfterAddingFile(fileItem) {
    this.uploading = true;
    fileItem.withCredentials = false;
    this.save().subscribe(
      () => {
        fileItem.url = this.apiService.endpoint + '/maps/' + this.map.id + '/families';
        this.uploader.uploadAll();
      }
    );
    return { fileItem: fileItem };
  }

  // save the map options
  save() {
    const data = {
      map: _.pick(
          this.map,
          ['name', 'show_micro', 'show_mac', 'show_dsp', 'show_other', 'show_dspc', 'show_partners', 'nurseries_by_family', 'nurseries_max_distance']
        )
    };
    let url: string;
    let method: string;
    if ( !this.map.id ) {
      url = '/users/' + this.user.id + '/maps';
      method = 'POST';
    } else {
      url = '/maps/' + this.map.id;
      method = 'PUT';
    }

    const source = this.apiService.postResource(url, data, method);
    source.subscribe(
        (map) => {
          if (!this.map.id) {
              this.location.replaceState('/maps/' + map.id + '/edit');
          } else {
            this.apiService.clearCache('/nurseries?map_id=' + this.map.id);
          }
          this.map = map;
        },
        (err) => console.log('error saving the map', err)
      );
    return source;
  }

  submit() {
    this.saving = true;
    this.save().subscribe(
      () => {
        this.save_error = '';
        this.saving = false;
        this.geocode();
      },
      (err) => {
        // @todo parse error
        this.save_error = 'Echec de la sauvegarde';
        this.saving = false;
      }
    );
  }

  geocode() {
    this.edition_step = 'geocoding';
    this.addressErrors = _.filter(this.map.families, (f) => f.address.geocode_status !== 1);
  }

  get show_errors() {
    return this._show_errors && !!this.addressErrors.length;
  }

  set show_errors(show) {
    this._show_errors = show;
  }

  toggleErrors(event) {
    this.show_errors = !this.show_errors;
    event.preventDefault();
  }

  removeFamily($event, family) {
    $event.preventDefault();
    let i: number;
    i = _.findIndex(this.addressErrors, (o) => o.id === family.id);
    this.addressErrors.splice(i, 1);
    i = _.findIndex(this.map.families, (o) => o.id === family.id);
    this.map.families.splice(i, 1);
    this.apiService.deleteResource('/families/' + family.id).subscribe(
      () => {}
    );
  }

  editFamily($event, family) {
    $event.preventDefault();

    const ei = _.findIndex(this.addressErrors, (o) => o.id === family.id);
    const i = _.findIndex(this.map.families, (o) => o.id === family.id);

    family.address.geocode_status = 1;
    this.apiService.postResource(
        '/families/' + family.id + '/address',
        {address: _.pick(family.address, ['latitude', 'longitude', 'geocode_status'])},
        'PUT'
    ).subscribe(
      (res) => {
        this.map.families[i].address = res;
        if (res.geocode_status === 1) {
            this.addressErrors.splice(ei, 1);
        }
      },
      (err) => {
        family.address.geocode_status = -1;
      }
    );
  }

}
