import { Address } from './address'

export class Nursery {
    id: number;
    nature: string;
    name: string;
    address: Address = new Address();
}
