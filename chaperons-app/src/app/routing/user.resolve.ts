import { Injectable } from '@angular/core';
import { Router, Resolve,
         ActivatedRouteSnapshot } from '@angular/router';

import { Observable } from 'rxjs/Observable';
import '../rxjs.operators.ts';

import { ApiService } from '../services/api';
import { AuthService } from '../services/auth';
import { Map } from '../models/map';

@Injectable()
export class UserResolve implements Resolve<any> {
  constructor(private authService: AuthService, private router: Router) {}

  resolve(route: ActivatedRouteSnapshot): Observable<any> | Promise<any> | any {

    const source = this.authService.loadUser();

    if ( !source ) {
      this.router.navigate(['/login']);
      return false;
    }

    if ( source instanceof Observable ) {
      return source.catch((err: any) => {
        this.router.navigateByUrl('/login');
        return Observable.of(false);
      });
    }
  }
}
