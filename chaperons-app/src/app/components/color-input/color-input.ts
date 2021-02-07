import { Component, EventEmitter, Input, Output  } from '@angular/core';

@Component({
    selector: 'chaperons-color-input',
    template: `
    <div [hidden]="show_input" (click)="toggle()" class="color-input-label">
        <i class="fa fa-paint-brush" aria-hidden="true" [style.border-color]="_color" [style.color]="_color"></i> {{ label }}
    </div>
    <div [hidden]="!show_input">
        <form class="form-inline">
            <div class="form-group">
                <input type="text" name="color" [(ngModel)]="_color" class="form-control color-input" />
            </div>
            <button type="button" class="btn btn-primary" (click)="submit()">ok</button>
        </form>
    </div>
    `,
    styles: [
        ':host {display: block;}',
        'i {border: 1px solid #ccc}',
        '.color-input-label {cursor: pointer}',
        'input.color-input {width: 90px;}'
    ]
})
export class ColorInputComponent {
    _color: string;

    @Input()
    label = 'Légende';

    @Output()
    changed = new EventEmitter<string>();

    show_input = false;

    get color() {
        return this._color;
    }

    @Input()
    set color(c) {
        this._color = c;
    }

    toggle() {
        this.show_input = !this.show_input;
    }

    submit() {
        this.changed.emit(this._color);
        this.show_input = false;
    }
}
