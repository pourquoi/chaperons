import { Injectable } from '@angular/core';
import { Router, Resolve,
         ActivatedRouteSnapshot } from '@angular/router';

import { Observable } from 'rxjs/Observable';
import '../rxjs.operators.ts';

import { ApiService } from '../services/api';
import { AuthService } from '../services/auth';
import { Map } from '../models/map';

@Injectable()
export class StatsResolve implements Resolve<any> {
  constructor(private apiService: ApiService, private router: Router) {}

  resolve(route: ActivatedRouteSnapshot): Observable<any>|boolean {
    return this.apiService.getResource('/nurseries/stats').catch(
      (err: any) => {
        this.router.navigate(['/error']);
        return Observable.of(false);
      });
  }
}
