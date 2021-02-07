/*
import { NgModule } from '@angular/core';
import { BrowserModule } from '@angular/platform-browser';

import { AppComponent } from './app.component';

@NgModule({
  declarations: [
    AppComponent
  ],
  imports: [
    BrowserModule
  ],
  providers: [],
  bootstrap: [AppComponent]
})
export class AppModule { }
*/
import { BrowserModule } from '@angular/platform-browser';
import { NgModule } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { HttpClientModule } from '@angular/common/http';

import { FileUploadModule } from 'ng2-file-upload';

import { AuthService } from './services/auth';
import { ApiService } from './services/api';

import { AppRoutingModule } from './routing/app-routing.module';
import { AppComponent } from './app.component';

import { GmapLoaderService } from './services/gmap-loader';

// page components
import { ErrorComponent } from './components/error/error';
import { MapsComponent } from './components/maps/maps';
import { MapEditComponent } from './components/map-edit/map-edit';
import { MapDetailComponent } from './components/map-detail/map-detail';
import { MapRenderComponent } from './components/map-render/map-render';
import { LoginComponent } from './components/login/login';

// selector components
import { HeaderComponent } from './components/header/header';
import { MapGmapComponent } from './components/map-gmap/map-gmap';
import { ColorInputComponent } from './components/color-input/color-input';
import { MapHistogramComponent } from './components/map-histogram/map-histogram'
import { MapStatsTableComponent } from './components/map-stats-table/map-stats-table';

@NgModule({
  declarations: [
    HeaderComponent,
    ErrorComponent,
    AppComponent,
    LoginComponent,
    MapsComponent,
    MapDetailComponent,
    MapEditComponent,
    LoginComponent,
    MapRenderComponent,
    MapGmapComponent,
    ColorInputComponent,
    MapHistogramComponent,
    MapStatsTableComponent
  ],
  imports: [
    BrowserModule,
    FormsModule,
    HttpClientModule,
    FileUploadModule,
    AppRoutingModule
  ],
  providers: [
    ApiService,
    AuthService,
    GmapLoaderService
  ],
  bootstrap: [AppComponent]
})
export class AppModule { }

