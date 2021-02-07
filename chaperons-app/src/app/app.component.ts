/*
import { Component } from '@angular/core';

@Component({
  selector: 'app-root',
  templateUrl: './app.component.html',
  styleUrls: ['./app.component.css']
})
export class AppComponent {
  title = 'chaperons';
}
*/

/// <reference path="../../node_modules/@types/googlemaps/index.d.ts" />

import { Component } from '@angular/core';

@Component({
  selector: 'app-root',
  template: `
  <div>
    <router-outlet></router-outlet>
  </div>
  `
})
export class AppComponent {
}
