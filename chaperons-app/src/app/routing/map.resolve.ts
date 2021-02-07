import { Injectable } from '@angular/core';
import { Router, Resolve,
         ActivatedRouteSnapshot } from '@angular/router';

import { Observable } from 'rxjs/Observable';
import '../rxjs.operators.ts';

import { ApiService } from '../services/api';
import { AuthService } from '../services/auth';
import { Map } from '../models/map';

@Injectable()
export class MapResolve implements Resolve<any> {
  constructor(private apiService: ApiService, private router: Router) {}

  resolve(route: ActivatedRouteSnapshot): Observable<any>|boolean {
    const id = +route.params.id;
    return this.apiService.getResource('/maps/' + id)
    .map((r) => (r as Map))
    .catch(
      (err: any) => {
        this.router.navigate(['/error']);
        return Observable.of(false);
      });
  }
}

