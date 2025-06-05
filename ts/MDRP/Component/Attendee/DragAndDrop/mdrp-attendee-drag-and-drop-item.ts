/**
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International
 */

import MDRPAttendeeDragAndDropBoxElement from "./mdrp-attendee-drag-and-drop-box";
import UiDropdownSimple from "WoltLabSuite/Core/Ui/Dropdown/Simple";
import WoltlabCoreDialogElement from "WoltLabSuite/Core/Element/woltlab-core-dialog";
import { Autobind } from "./Autobind";
import { DragContext } from "./DragContext";
import { availableCharacters } from "../../../Api/Events/AvailableCharacters";
import { createAttendee } from "../../../Api/Attendees/CreateAttendee";
import { dialogFactory } from "WoltLabSuite/Core/Component/Dialog";
import { deleteAttendee } from "../../../Api/Attendees/DeleteAttendee";
import { getPhrase } from "WoltLabSuite/Core/Language";
import { renderAttendee } from "../../../Api/Attendees/RenderAttendee";
import { show as showNotification } from "WoltLabSuite/Core/Ui/Notification";
import { updateAttendeeStatus } from "../../../Api/Attendees/UpdateAttendeeStatus";

export class MDRPAttendeeDragAndDropItemElement extends HTMLElement {
  #dialog: WoltlabCoreDialogElement;
  #statusDialog: string;

  connectedCallback() {
    this.addEventListener("dragstart", (event) => {
      this.dragStartHandler(event);
    });

    this.addEventListener("dragend", (event) => {
      this.dragEndHandler(event);
    });

    if (this.menu) {
      this.#statusDialog = `
        <div class="section">
            <dl>
                <dt>${getPhrase("rp.event.raid.status")}</dt>
                <dd>
                    <select name="status">
                        <option value="0">${getPhrase("rp.event.raid.container.login")}</option>
                        <option value="3">${getPhrase("rp.event.raid.container.reserve")}</option>
                        <option value="2">${getPhrase("rp.event.raid.container.logout")}</option>
                    </select>
                </dd>
            </dl>
        </div>
        `;

      const updateStatusButton = this.menu.querySelector<HTMLElement>(".attendee__option--update-status");
      updateStatusButton?.addEventListener("click", (event) => {
        event.preventDefault();
        this.#updateStatus();
      });

      const switchCharacterButton = this.menu.querySelector<HTMLElement>(".attendee__option--character-switch");
      switchCharacterButton?.addEventListener("click", (event) => {
        event.preventDefault();
        void this.#switchCharacter();
      });
    }
  }

  @Autobind
  dragEndHandler(_: DragEvent): void {
    document.querySelectorAll(".attendeeBox").forEach((attendeeBox: HTMLElement) => {
      attendeeBox.classList.remove("droppable");
      attendeeBox.classList.remove("selected");
    });
  }

  @Autobind
  dragStartHandler(event: DragEvent): void {
    event.dataTransfer!.effectAllowed = "move";

    const currentBox = this.closest<HTMLElement>(".attendeeBox");

    DragContext.set({
        attendeeId: this.attendeeId,
        droppableTo: this.droppableTo,
        currentStatus: parseInt(currentBox!.getAttribute("status")!),
        distribution: currentBox!.getAttribute("distribution")!,
        id: this.id,
    });

    document.querySelectorAll(".attendeeBox").forEach((attendeeBox: HTMLElement) => {
      const droppable = attendeeBox.getAttribute("droppable")!;
      const droppableTo = this.droppableTo;
      if (!droppableTo.includes(droppable)) return;

      attendeeBox.classList.add("droppable");
    });
  }

  async #loadSwitchCharacter(attendeeId: number): Promise<void> {
    const response = await renderAttendee(attendeeId);
    if (!response.ok) {
      const validationError = response.error.getValidationError();
      if (validationError === undefined) {
        throw new Error("Unexpected validation error", { cause: response.error });
      }

      this.remove();
      return;
    }

    const box = document.querySelector<MDRPAttendeeDragAndDropBoxElement>(
      `mdrp-attendee-drag-and-drop-box[distribution="${response.value.distribution}"][status="${this.status}"]`,
    );
    const attendeeList = box?.querySelector<HTMLElement>(".attendeeList");
    attendeeList?.insertAdjacentHTML("beforeend", response.value.template);

    showNotification();
    this.remove();
  }

  async #switchCharacter(): Promise<void> {
    const { template } = (await availableCharacters(this.eventId)).unwrap();
    console.log(template);
    this.#showSwitchDialog(template);
  }

  #showSwitchDialog(template: string): void {
    const dialog = dialogFactory().fromHtml(template).asPrompt();
    const characterId = dialog.content.querySelector<HTMLSelectElement>('select[name="characterID"]');
    const role = dialog.content.querySelector<HTMLSelectElement>('select[name="role"]');
    dialog.addEventListener("primary", async () => {
      (await deleteAttendee(this.attendeeId)).unwrap();
      this.dispatchEvent(new CustomEvent("delete"));

      const response = await createAttendee(this.eventId, characterId!.value, role!.value, this.status);
      if (!response.ok) {
        const validationError = response.error.getValidationError();
        if (validationError === undefined) {
          throw new Error("Unexpected validation error", { cause: response.error });
        }

        this.remove();
        return;
      }

      void this.#loadSwitchCharacter(response.value.attendeeId);
    });

    dialog.show(getPhrase("rp.character.selection"));
  }

  #updateStatus(): void {
    if (!this.#dialog) {
      this.#dialog = dialogFactory().fromHtml(this.#statusDialog).asPrompt();
      const status = this.#dialog.content.querySelector<HTMLSelectElement>('select[name="status"]');
      this.#dialog.addEventListener("primary", async () => {
        const response = await updateAttendeeStatus(this.attendeeId, this.distribution, parseInt(status!.value));
        if (!response.ok) {
          const validationError = response.error.getValidationError();
          if (validationError === undefined) {
            throw new Error("Unexpected validation error", { cause: response.error });
          }
          dialogFactory().fromHtml(`<p>${validationError.message}</p>`).asAlert();
          return;
        }

        const dragAndDropBox = document.querySelector(
          `mdrp-attendee-drag-and-drop-box[status="${status!.value}"][distribution="${this.distribution}"]`,
        );
        const attendeeList = dragAndDropBox?.querySelector<HTMLElement>(".attendeeList");
        attendeeList?.insertAdjacentElement("beforeend", this);
      });
    }

    this.#dialog.show(getPhrase("rp.event.raid.updateStatus"));
  }

  get attendeeId(): number {
    return parseInt(this.getAttribute("attendee-id")!);
  }

  get box(): MDRPAttendeeDragAndDropBoxElement {
    return this.closest<MDRPAttendeeDragAndDropBoxElement>("mdrp-attendee-drag-and-drop-box")!;
  }

  get distribution(): string {
    return this.getAttribute("distribution")!;
  }

  get droppableTo(): string {
    return this.getAttribute("droppable-to")!;
  }

  get eventId(): number {
    return parseInt(this.getAttribute("event-id")!);
  }

  get menu(): HTMLElement | undefined {
    let menu = UiDropdownSimple.getDropdownMenu(`attendeeOptions${this.attendeeId}`);

    if (menu === undefined) {
      menu = this.querySelector<HTMLElement>(".attendee__menu .dropdownMenu") || undefined;
    }

    return menu;
  }

  get status(): number {
    return parseInt(this.box.getAttribute("status")!);
  }
}

window.customElements.define("mdrp-attendee-drag-and-drop-item", MDRPAttendeeDragAndDropItemElement);

export default MDRPAttendeeDragAndDropItemElement;
