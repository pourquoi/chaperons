import { Injectable } from '@angular/core';

import { Observable } from 'rxjs/Observable';
import '../rxjs.operators.ts';

import {
  CanActivate, CanLoad, Router, Route,
  ActivatedRouteSnapshot,
  RouterStateSnapshot
} from '@angular/router';

import { ApiService } from '../services/api';
import { AuthService } from '../services/auth';

import { User } from '../models/user';


@Injectable()
export class AuthGuard implements CanActivate, CanLoad {
  constructor(private authService: AuthService, private router: Router) {}

  canActivate(route: ActivatedRouteSnapshot, state: RouterStateSnapshot): boolean {
    const url: string = state.url;

    return this.checkLogin(url);
  }

  canLoad(route: Route): boolean {
    const url = `/${route.path}`;

    return this.checkLogin(url);
  }

  checkLogin(url: string): boolean {

    if (this.authService.loggedin) { return true; }

    // Store the attempted URL for redirecting
    this.authService.redirectUrl = url;

    // Navigate to the login page with extras
    this.router.navigate(['/login']);
    return false;
  }
}
