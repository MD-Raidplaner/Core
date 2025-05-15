/**
 * Provides participation in events.
 *
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International
 */

import { dboAction } from "WoltLabSuite/Core/Ajax";
import { dialogFactory } from "WoltLabSuite/Core/Component/Dialog";
import DomUtil from "WoltLabSuite/Core/Dom/Util";
import { getPhrase } from "WoltLabSuite/Core/Language";
import { show as showNotification } from "WoltLabSuite/Core/Ui/Notification";
import User from "WoltLabSuite/Core/User";
import WoltlabCoreDialogElement from "WoltLabSuite/Core/Element/woltlab-core-dialog";

export class UiEventAppointmentChange {
  #dialog?: WoltlabCoreDialogElement;
  #eventId: number;

  constructor(button: HTMLButtonElement) {
    this.#eventId = ~~button.dataset.eventId!;

    button.addEventListener("click", () => {
      this.#show();
    });
  }

  #createDialog(): WoltlabCoreDialogElement {
    const dialog = dialogFactory().fromId("appointmentChangeDialog").asPrompt();
    const content = dialog.content;

    dialog.addEventListener("primary", () => {
      const status = content.querySelector<HTMLInputElement>('input[name="status"]:checked')!;

      void (async () => {
        const response = (await dboAction("appointmentSetStatus", "rp\\data\\event\\EventAction")
          .payload({
            eventID: this.#eventId,
            status: status.value,
          })
          .disableLoadingIndicator()
          .dispatch()) as ResponseAppointment;

        document.querySelectorAll<HTMLOListElement>(".jsEventAppointment").forEach((appointment: HTMLOListElement) => {
          const appointmentStatus = appointment.dataset.status!;

          const element = appointment.querySelector<HTMLLIElement>(`li[data-object-id="${User.userId}"]`);
          if (element && appointmentStatus !== status.value) {
            element.remove();
          } else if (!element && appointmentStatus === status.value) {
            const notice = appointment.querySelector("woltlab-core-notice");
            if (notice) {
              appointment.innerHTML = "";
            }

            DomUtil.insertHtml(response.template, appointment, "prepend");
          }
        });

        showNotification();
      })();
    });

    return dialog;
  }

  #show(): void {
    if (!this.#dialog) {
      this.#dialog = this.#createDialog();
    }

    this.#dialog.show(getPhrase("rp.event.participation"));
  }
}

export default UiEventAppointmentChange;

type ResponseAppointment = {
  template: string;
};
