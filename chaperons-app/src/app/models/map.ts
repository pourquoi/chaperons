import { Family } from './family';

export class Map {
    capture_filename: string = null;
    id: number;
    name = 'Carte sans titre';
    show_mac = true;
    show_micro = true;
    show_dsp = true;
    show_partners = true;
    families: Array<Family> = [];
    fill_color_family: string;
    fill_color_nursery_owned: string;
    fill_color_nursery: string;
    ne_lat: number;
    ne_lng: number;
    sw_lat: number;
    sw_lng: number;
    zoom: number;
    center_lat: number;
    center_lng: number;
    height: number;
    width: number;
    style_name: string;
    nurseries_by_family = 30;
    nurseries_max_distance = 30000;
    created_at: string;
}
