import { Component } from '@angular/core';

import { AuthService } from '../../services/auth';

@Component({
  selector: 'app-header',
  templateUrl: './header.html',
  styleUrls: ['./header.css']
})

export class HeaderComponent
{
    constructor(public authService: AuthService) {}

    logout() {
      this.authService.logout();
    }
}