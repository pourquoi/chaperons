import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';

import { AuthGuard } from './auth.guard';
import { UserResolve } from './user.resolve';
import { MapResolve } from './map.resolve';
import { StatsResolve } from './stats.resolve';
import { NurseriesResolve } from './nurseries.resolve';

import { ErrorComponent } from '../components/error/error';
import { LoginComponent } from '../components/login/login';
import { MapsComponent } from '../components/maps/maps';
import { MapEditComponent } from '../components/map-edit/map-edit';
import { MapDetailComponent } from '../components/map-detail/map-detail';
import { MapRenderComponent } from '../components/map-render/map-render';

const routes: Routes = [
  { path: 'error', component: ErrorComponent },
  { path: 'login', component: LoginComponent },
  { path: 'logout', component: LoginComponent },
  { path: 'maps',
    component: MapsComponent,
    canActivate: [AuthGuard],
    resolve: {user: UserResolve} },
  { path: 'maps/new',
    component: MapEditComponent,
    canActivate: [AuthGuard],
    resolve: {user: UserResolve} },
  { path: 'maps/:id/edit',
    component: MapEditComponent,
    canActivate: [AuthGuard],
    resolve: {user: UserResolve, map: MapResolve} },
  { path: 'maps/:id/render',
    component: MapRenderComponent,
    canActivate: [AuthGuard],
    resolve: {user: UserResolve, map: MapResolve} },
  { path: 'maps/:id',
    component: MapDetailComponent,
    canActivate: [AuthGuard],
    resolve: {user: UserResolve, map: MapResolve} },

  { path: '', redirectTo: '/maps', pathMatch: 'full' }
];

@NgModule({
  imports: [RouterModule.forRoot(routes)],
  exports: [RouterModule],
  providers: [AuthGuard, UserResolve, StatsResolve, MapResolve, NurseriesResolve]
})
export class AppRoutingModule { }
