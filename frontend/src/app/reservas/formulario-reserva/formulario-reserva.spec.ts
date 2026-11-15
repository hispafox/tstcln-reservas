import { TestbedHarnessEnvironment } from '@angular/cdk/testing/testbed';
import { TestBed } from '@angular/core/testing';
import { fireEvent, render, screen } from '@testing-library/angular';
import userEvent from '@testing-library/user-event';
import { of, throwError } from 'rxjs';
import { salaNorte } from '../../../testing/datos-de-prueba';
import { ApiReservas } from '../api-reservas';
import { ReservaRechazadaError } from '../errores';
import { Reserva } from '../reserva';
import { FormularioReserva } from './formulario-reserva';
import { FormularioReservaHarness } from './formulario-reserva.harness';

const reservaCreada: Reserva = {
  id: 7,
  sala: salaNorte.id,
  inicio: '2027-03-04T10:00:00+01:00',
  fin: '2027-03-04T11:00:00+01:00',
  persona: 'Ana',
  asistentes: 1,
  estado: 'confirmada',
};

describe('FormularioReserva', () => {
  it('desactiva el botón mientras falten datos', async () => {
    await render(FormularioReserva, {
      inputs: { sala: salaNorte },
      providers: [{ provide: ApiReservas, useValue: { crear: vi.fn() } }],
    });

    expect(
      screen.getByRole('button', { name: 'Reservar' }),
    ).toHaveProperty('disabled', true);
  });

  it('desactiva el botón mientras falten datos, montado con TestBed a mano', async () => {
    TestBed.configureTestingModule({
      providers: [{ provide: ApiReservas, useValue: { crear: vi.fn() } }],
    });
    const fixture = TestBed.createComponent(FormularioReserva);
    fixture.componentRef.setInput('sala', salaNorte);
    await fixture.whenStable();

    const boton = screen.getByRole('button', { name: 'Reservar' });
    expect(boton).toHaveProperty('disabled', true);
  });

  it('desactiva el botón si la hora de fin es anterior a la de inicio', async () => {
    TestBed.configureTestingModule({ providers: [{ provide: ApiReservas, useValue: { crear: vi.fn() } }] });
    const fixture = TestBed.createComponent(FormularioReserva);
    fixture.componentRef.setInput('sala', salaNorte);
    const formulario = await TestbedHarnessEnvironment.harnessForFixture(fixture, FormularioReservaHarness);

    await formulario.rellenar({ persona: 'Ana', inicio: '2027-03-04T11:00', fin: '2027-03-04T10:00' });

    expect(await formulario.puedeReservar()).toBe(false);
    expect(await formulario.mensajes()).toContain('La hora de fin tiene que ser posterior a la de inicio');
  });

  it('enseña el motivo si la API rechaza la reserva', async () => {
    const crear = vi.fn(() =>
      throwError(() =>
        new ReservaRechazadaError(
          'La sala ya está reservada en esa franja',
        ),
      ),
    );
    await render(FormularioReserva, {
      inputs: { sala: salaNorte },
      providers: [{ provide: ApiReservas, useValue: { crear } }],
    });
    const usuario = userEvent.setup();

    await usuario.type(screen.getByLabelText('Persona'), 'Ana');
    fireEvent.input(screen.getByLabelText('Hora de inicio'), {
      target: { value: '2027-03-04T10:00' },
    });
    fireEvent.input(screen.getByLabelText('Hora de fin'), {
      target: { value: '2027-03-04T11:00' },
    });
    await usuario.click(screen.getByRole('button', { name: 'Reservar' }));

    expect(
      await screen.findByText('La sala ya está reservada en esa franja'),
    ).toBeTruthy();
  });

  it('quita el motivo del rechazo cuando la siguiente reserva sale bien', async () => {
    const crear = vi
      .fn()
      .mockReturnValueOnce(
        throwError(() => new ReservaRechazadaError('La sala ya está reservada en esa franja')),
      )
      .mockReturnValueOnce(of(reservaCreada));
    await render(FormularioReserva, {
      inputs: { sala: salaNorte },
      providers: [{ provide: ApiReservas, useValue: { crear } }],
    });
    const usuario = userEvent.setup();
    await usuario.type(screen.getByLabelText('Persona'), 'Ana');
    fireEvent.input(screen.getByLabelText('Hora de inicio'), { target: { value: '2027-03-04T10:00' } });
    fireEvent.input(screen.getByLabelText('Hora de fin'), { target: { value: '2027-03-04T11:00' } });
    await usuario.click(screen.getByRole('button', { name: 'Reservar' }));
    await screen.findByText('La sala ya está reservada en esa franja');

    await usuario.click(screen.getByRole('button', { name: 'Reservar' }));

    expect(screen.queryByText('La sala ya está reservada en esa franja')).toBeNull();
  });

  it('emite la reserva creada', async () => {
    const crear = vi.fn(() => of(reservaCreada));
    const { fixture } = await render(FormularioReserva, {
      inputs: { sala: salaNorte },
      providers: [{ provide: ApiReservas, useValue: { crear } }],
    });
    const emitidas: Reserva[] = [];
    fixture.componentInstance.reservaCreada.subscribe((reserva) =>
      emitidas.push(reserva),
    );
    const usuario = userEvent.setup();

    await usuario.type(screen.getByLabelText('Persona'), 'Ana');
    fireEvent.input(screen.getByLabelText('Hora de inicio'), { target: { value: '2027-03-04T10:00' } });
    fireEvent.input(screen.getByLabelText('Hora de fin'), { target: { value: '2027-03-04T11:00' } });
    await usuario.click(screen.getByRole('button', { name: 'Reservar' }));

    expect(emitidas).toEqual([reservaCreada]);
    expect(crear).toHaveBeenCalledWith(
      expect.objectContaining({ sala: salaNorte.id, persona: 'Ana' }),
    );
  });
});
