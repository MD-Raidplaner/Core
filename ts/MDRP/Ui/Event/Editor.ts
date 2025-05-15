/**
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International
 */

import { stringToBool } from "WoltLabSuite/Core/Core";
import { getPhrase } from "WoltLabSuite/Core/Language";
import { confirmationFactory } from "WoltLabSuite/Core/Component/Confirmation";
import { show as showNotification } from "WoltLabSuite/Core/Ui/Notification";
import { trashEvent } from "../../Api/Events/TrashEvent";
import { dialogFactory } from "WoltLabSuite/Core/Component/Dialog";
import { restoreEvent } from "../../Api/Events/RestoreEvent";
import { enableDisableEvent } from "../../Api/Events/EnableDisableEvent";
import { deleteEvent } from "../../Api/Events/DeleteEvent";
import DomUtil from "WoltLabSuite/Core/Dom/Util";
import { cancelEvent } from "../../Api/Events/CancelEvent";

export class UiEventEditor {
  readonly #elements: Map<string, HTMLLIElement> = new Map<string, HTMLLIElement>();
  readonly #event: HTMLElement;
  readonly #eventIcons: HTMLHeadingElement;
  readonly #eventId: number;

  constructor() {
    this.#event = document.querySelector<HTMLElement>(".event")!;
    this.#eventId = parseInt(this.#event.dataset.eventId!);
    this.#eventIcons = document.querySelector<HTMLHeadingElement>(".rpEventHeader .contentHeaderTitle .contentTitle")!;

    this.#rebuild();
  }

  async #cancelExecute(): Promise<void> {
    const result = await this.#cancelConfirmation();
    if (result) {
      const response = await cancelEvent(this.#eventId);
      if (!response.ok) {
        const validationError = response.error.getValidationError();
        if (validationError === undefined) {
          throw new Error("Unexpected validation error", { cause: response.error });
        }
        dialogFactory().fromHtml(`<p>${validationError.message}</p>`).asAlert();
        return;
      }

      showNotification();

      window.location.reload();
    }
  }

  async #cancelConfirmation(): Promise<ResultCancelConfirmation> {
    const title = this.#event.dataset.title!;
    const question = getPhrase("rp.event.raid.cancel.confirmMessage", { title });

    const dialog = dialogFactory().withoutContent().asConfirmation();
    dialog.show(question);

    return new Promise<ResultCancelConfirmation>((resolve) => {
      dialog.addEventListener("primary", () => {
        resolve({
          result: true,
        });
      });

      dialog.addEventListener("cancel", () => {
        resolve({
          result: false,
        });
      });
    });
  }

  #click(optionName: string, event: Event): void {
    event.preventDefault();

    const element = this.#elements.get(optionName);

    if (optionName === "editLink" || optionName === "transform") {
      window.location.href = element!.dataset.link!;
    } else {
      this.#execute(optionName);
    }
  }

  async #deleteExecute(): Promise<void> {
    const title = this.#event.dataset.title!;
    const result = await confirmationFactory().delete(title);

    if (result) {
      const response = await deleteEvent(this.#eventId);
      if (!response.ok) {
        const validationError = response.error.getValidationError();
        if (validationError === undefined) {
          throw new Error("Unexpected validation error", { cause: response.error });
        }
        dialogFactory().fromHtml(`<p>${validationError.message}</p>`).asAlert();
        return;
      }

      showNotification();

      window.location.href = `${window.RP_API_URL}index.php?calendar`;
    }
  }

  async #enableDisableExecute(): Promise<void> {
    const isEnabled = stringToBool(this.#event.dataset.enabled!);

    const response = await enableDisableEvent(this.#eventId, isEnabled);
    if (!response.ok) {
      const validationError = response.error.getValidationError();
      if (validationError === undefined) {
        throw new Error("Unexpected validation error", { cause: response.error });
      }
      dialogFactory().fromHtml(`<p>${validationError.message}</p>`).asAlert();
      return;
    }

    this.#event.dataset.enabled = isEnabled ? "false" : "true";

    const isDisabled = !stringToBool(this.#event.dataset.enabled);
    let iconIsDisabled = document.querySelector<HTMLElement>(".rpEventHeader .jsIsDisabled");
    if (isDisabled && iconIsDisabled === null) {
      iconIsDisabled = document.createElement("span");
      iconIsDisabled.classList.add("badge", "label", "green", "jsIsDisabled");
      iconIsDisabled.innerHTML = getPhrase("wcf.message.status.disabled");
      this.#eventIcons.appendChild(iconIsDisabled);
    } else if (!isDisabled && iconIsDisabled !== null) {
      iconIsDisabled.remove();
    }

    showNotification();
    this.#rebuild();
  }

  #execute(optionName: string): void {
    if (!this.#validate(optionName)) return;

    switch (optionName) {
      case "cancel":
        void this.#cancelExecute();
        break;

      case "delete":
        void this.#deleteExecute();
        break;

      case "restore":
        void this.#restoreExecute();
        break;

      case "trash":
        void this.#trashExecute();
        break;

      case "enable":
      case "disable":
        void this.#enableDisableExecute();
        break;
    }
  }

  #rebuild(): void {
    let showDropdown = false;

    document.querySelectorAll(".jsEventDropdownItems > li").forEach((element: HTMLLIElement) => {
      const optionName = element.dataset.optionName;
      if (optionName) {
        if (this.#validate(optionName)) {
          DomUtil.show(element);
          showDropdown = true;
        } else {
          DomUtil.hide(element);
        }

        if (!this.#elements.get(optionName)) {
          element.addEventListener("click", (ev) => this.#click(optionName, ev));

          this.#elements.set(optionName, element);

          if (optionName === "editLink") {
            const dropdownToggle = document.querySelector<HTMLAnchorElement>(".jsEventDropdown > .dropdownToggle");
            dropdownToggle?.addEventListener("dblclick", () => {
              element.click();
            });
          }
        }
      }
    });

    const dropdownMenu = document.querySelector<HTMLElement>(".jsEventDropdown");
    if (!showDropdown) {
      dropdownMenu!.remove();
    } else {
      DomUtil.show(dropdownMenu!);
    }
  }

  async #restoreExecute(): Promise<void> {
    const title = this.#event.dataset.title!;
    const result = await confirmationFactory().restore(title);

    if (result) {
      const response = await restoreEvent(this.#eventId);
      if (!response.ok) {
        const validationError = response.error.getValidationError();
        if (validationError === undefined) {
          throw new Error("Unexpected validation error", { cause: response.error });
        }
        dialogFactory().fromHtml(`<p>${validationError.message}</p>`).asAlert();
        return;
      }

      const iconIsDeleted = document.querySelector<HTMLElement>(".rpEventHeader .jsIsDeleted");
      if (iconIsDeleted !== null) {
        iconIsDeleted.remove();
      }

      this.#event.dataset.deleted = "false";

      showNotification();
      this.#rebuild();
    }
  }

  async #trashExecute(): Promise<void> {
    const title = this.#event.dataset.title!;
    const { result } = await confirmationFactory().softDelete(title);

    if (result) {
      const response = await trashEvent(this.#eventId);
      if (!response.ok) {
        const validationError = response.error.getValidationError();
        if (validationError === undefined) {
          throw new Error("Unexpected validation error", { cause: response.error });
        }
        dialogFactory().fromHtml(`<p>${validationError.message}</p>`).asAlert();
        return;
      }

      this.#event.dataset.deleted = "true";

      let iconIsDeleted = document.querySelector<HTMLElement>(".rpEventHeader .jsIsDeleted");
      if (iconIsDeleted === null) {
        iconIsDeleted = document.createElement("span");
        iconIsDeleted.classList.add("badge", "label", "red", "jsIsDeleted");
        iconIsDeleted.innerHTML = getPhrase("wcf.message.status.deleted");
        this.#eventIcons.appendChild(iconIsDeleted);
      }

      showNotification();
      this.#rebuild();
    }
  }

  #validate(optionName: string): boolean {
    switch (optionName) {
      case "cancel":
        if (!stringToBool(this.#event.dataset.canCancel!)) {
          return false;
        }

        if (!stringToBool(this.#event.dataset.canceled!)) {
          return true;
        }
        break;

      case "delete":
        if (!stringToBool(this.#event.dataset.canDelete!)) {
          return false;
        }

        if (stringToBool(this.#event.dataset.deleted!)) {
          return true;
        }
        break;

      case "restore":
        if (!stringToBool(this.#event.dataset.canRestore!)) {
          return false;
        }

        if (stringToBool(this.#event.dataset.deleted!)) {
          return true;
        }
        break;

      case "trash":
        if (!stringToBool(this.#event.dataset.canTrash!)) {
          return false;
        }

        if (!stringToBool(this.#event.dataset.deleted!)) {
          return true;
        }
        break;

      case "enable":
      case "disable":
        if (!stringToBool(this.#event.dataset.canEdit!)) {
          return false;
        }

        if (stringToBool(this.#event.dataset.canceled!)) {
          return false;
        }

        if (stringToBool(this.#event.dataset.deleted!)) {
          return false;
        }

        if (stringToBool(this.#event.dataset.enabled!)) {
          return optionName === "disable";
        } else {
          return optionName === "enable";
        }
        break;

      case "editLink":
        if (stringToBool(this.#event.dataset.canEdit!)) {
          return true;
        }
        break;

      case "transform":
        if (stringToBool(this.#event.dataset.canTransform!)) {
          return true;
        }
        break;
    }

    return false;
  }
}

export default UiEventEditor;

type ResultCancelConfirmation = {
  result: boolean;
};
