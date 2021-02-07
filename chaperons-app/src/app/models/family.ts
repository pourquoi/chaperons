import { Address } from './address';
import { NurserySelection } from './nursery-selection';

export class Family {
    id: number;
    address: Address = new Address();
    nurseries: NurserySelection[] = [];
}
