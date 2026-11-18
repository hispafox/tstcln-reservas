import { TestBed } from '@angular/core/testing';
import { of } from 'rxjs';
import { salaNorte, salaSur } from '../../testing/datos-de-prueba';
import { ApiSalas } from './datos/api-salas';
import { BusquedaDeSalas } from './busqueda-de-salas';
import { Sala } from './dominio/sala';

describe('BusquedaDeSalas', () => {
  beforeEach(() => vi.useFakeTimers());
  afterEach(() => vi.useRealTimers());

  it('espera a que se deje de escribir para buscar', () => {
    const api = { listar: vi.fn(() => of([salaNorte, salaSur])) };
    TestBed.configureTestingModule({ providers: [{ provide: ApiSalas, useValue: api }] });
    const busqueda = TestBed.inject(BusquedaDeSalas);
    const recibidos: Sala[][] = [];
    busqueda.resultados.subscribe((salas) => recibidos.push(salas));

    busqueda.escribir('n');
    vi.advanceTimersByTime(100);
    busqueda.escribir('no');
    vi.advanceTimersByTime(100);
    busqueda.escribir('nor');
    vi.advanceTimersByTime(300);

    expect(api.listar).toHaveBeenCalledTimes(1);
    expect(recibidos).toEqual([[salaNorte]]);
  });
});
