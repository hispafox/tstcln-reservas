import { provideHttpClient } from '@angular/common/http';
import { HttpTestingController, provideHttpClientTesting } from '@angular/common/http/testing';
import { TestBed } from '@angular/core/testing';
import { render, screen } from '@testing-library/angular';
import userEvent from '@testing-library/user-event';
import { filas } from '../../../testing/filas';
import { ListaReservas } from './lista-reservas';

describe('ListaReservas', () => {
  it('enseña las próximas confirmadas de Ana, ordenadas por fecha', async () => {
    const { fixture } = await render(ListaReservas, {
      providers: [provideHttpClient(), provideHttpClientTesting()],
    });
    const http = TestBed.inject(HttpTestingController);

    http.expectOne('/salas').flush([
      { id: 1, nombre: 'Sala Norte' },
      { id: 2, nombre: 'Sala Sur' },
    ]);
    http.expectOne('/reservas?persona=Ana').flush([
      reserva(1, 2, '2027-03-05T10:00:00+01:00', 'confirmada'),
      reserva(2, 1, '2027-03-04T10:00:00+01:00', 'confirmada'),
      reserva(3, 1, '2027-03-04T12:00:00+01:00', 'cancelada'),
    ]);
    await fixture.whenStable();

    expect(filas()).toEqual([
      'Sala Norte2027-03-04 10:0011:0060 min4ConfirmadaCancelar',
      'Sala Sur2027-03-05 10:0011:0060 min4ConfirmadaCancelar',
    ]);
  });

  it('sin el filtro y ordenada por sala, enseña todas', async () => {
    const { fixture } = await render(ListaReservas, {
      providers: [provideHttpClient(), provideHttpClientTesting()],
    });
    const http = TestBed.inject(HttpTestingController);
    http.expectOne('/salas').flush([
      { id: 1, nombre: 'Sala Norte' },
      { id: 2, nombre: 'Sala Sur' },
    ]);
    http.expectOne('/reservas?persona=Ana').flush([
      reserva(1, 2, '2027-03-05T10:00:00+01:00', 'confirmada'),
      reserva(2, 1, '2027-03-04T10:00:00+01:00', 'confirmada'),
      reserva(3, 1, '2027-03-04T12:00:00+01:00', 'cancelada'),
    ]);
    await fixture.whenStable();

    await userEvent.click(screen.getByRole('checkbox'));
    await userEvent.selectOptions(screen.getByRole('combobox'), 'sala');
    await fixture.whenStable();

    expect(filas()).toEqual([
      'Sala Norte2027-03-04 10:0011:0060 min4ConfirmadaCancelar',
      'Sala Norte2027-03-04 12:0013:0060 min4',
      'Sala Sur2027-03-05 10:0011:0060 min4ConfirmadaCancelar',
    ]);
  });

  it('al pulsar «Cancelar» pide el DELETE de esa reserva', async () => {
    const { fixture } = await render(ListaReservas, {
      providers: [provideHttpClient(), provideHttpClientTesting()],
    });
    const http = TestBed.inject(HttpTestingController);
    http.expectOne('/salas').flush([
      { id: 1, nombre: 'Sala Norte' },
      { id: 2, nombre: 'Sala Sur' },
    ]);
    http.expectOne('/reservas?persona=Ana').flush([
      reserva(1, 2, '2027-03-05T10:00:00+01:00', 'confirmada'),
      reserva(2, 1, '2027-03-04T10:00:00+01:00', 'confirmada'),
    ]);
    await fixture.whenStable();
    vi.spyOn(window, 'confirm').mockReturnValue(true);

    await userEvent.click(screen.getAllByRole('button', { name: 'Cancelar' })[0]);

    expect(http.expectOne({ method: 'DELETE', url: '/reservas/2' })).toBeTruthy();
  });

  describe('con el reloj falso', () => {
    beforeEach(() => vi.useFakeTimers());
    afterEach(() => vi.useRealTimers());

    it('al salir de la pantalla deja de pedir las reservas cada minuto', async () => {
      const { fixture } = await render(ListaReservas, {
        providers: [provideHttpClient(), provideHttpClientTesting()],
      });
      const http = TestBed.inject(HttpTestingController);
      http.expectOne('/salas').flush([]);
      http.expectOne('/reservas?persona=Ana').flush([]);

      fixture.destroy();
      vi.advanceTimersByTime(60_000);

      http.expectNone('/salas');
    });
  });
});

function reserva(id: number, sala: number, inicio: string, estado: string) {
  return { id, sala, inicio, fin: inicio.replace('T10', 'T11').replace('T12', 'T13'), persona: 'Ana', asistentes: 4, estado };
}
