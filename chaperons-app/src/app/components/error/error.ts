import { Component, OnInit } from '@angular/core';

import { Location } from '@angular/common';

@Component({
  templateUrl: './error.html',
  styleUrls: ['./error.css']
})

export class ErrorComponent implements OnInit {
    errorCode = 500;

    constructor(private location: Location) {}

    ngOnInit() {
        this.location.replaceState('/');
    }
}
