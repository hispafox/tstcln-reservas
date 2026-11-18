import { ChangeDetectionStrategy, Component, input, output } from '@angular/core';

/** La oferta de apuntarse a la lista de espera de una franja ocupada. Solo enseña y avisa. */
@Component({
  selector: 'app-oferta-lista-de-espera',
  templateUrl: './oferta-lista-de-espera.html',
  changeDetection: ChangeDetectionStrategy.OnPush,
})
export class OfertaListaDeEspera {
  readonly franja = input.required<{ inicio: string; fin: string }>();
  readonly apuntarme = output();
}
