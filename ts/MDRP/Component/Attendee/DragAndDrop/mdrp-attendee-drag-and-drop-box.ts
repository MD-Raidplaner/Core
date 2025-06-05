/**
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International
 */

import { Autobind } from "./Autobind";
import { dialogFactory } from "WoltLabSuite/Core/Component/Dialog";
import { updateAttendeeStatus } from "../../../Api/Attendees/UpdateAttendeeStatus";
import { show as showNotification } from "WoltLabSuite/Core/Ui/Notification";
import { DragContext } from "./DragContext";

export class MDRPAttendeeDragAndDropBoxElement extends HTMLElement {
  connectedCallback() {
    this.addEventListener("dragover", (event) => {
      this.dragOverHandler(event);
    });

    this.addEventListener("drop", (event) => {
      void this.dropHandler(event);
    });

    this.addEventListener("dragleave", (event) => {
      this.dragLeaveHandler(event);
    });
  }

  @Autobind
  dragOverHandler(event: DragEvent): void {
    event.preventDefault();

    if (!event.dataTransfer || event.dataTransfer.effectAllowed !== "move") return;

    const dragContext = DragContext.get();
    if (!dragContext) {
      console.warn("DragContext is not set, cannot handle drag over event.");
      return;
    }

    const droppable = this.droppable;
    const droppableTo = dragContext.droppableTo;
    if (!droppableTo.includes(droppable)) return;

    this.classList.add("selected");
  }

  @Autobind
  async dropHandler(event: DragEvent): Promise<void> {
    if (!event.dataTransfer || event.dataTransfer.effectAllowed !== "move") return;
    event.preventDefault();

    const dragContext = DragContext.get();
    if (!dragContext) {
      console.warn("DragContext is not set, cannot handle drop event.");
      return;
    }

    const droppable = this.droppable;
    const droppableTo = dragContext.droppableTo;
    if (!droppableTo.includes(droppable)) return;

    const distribution = this.distribution;
    const status = this.status;

    if (
      status === dragContext.currentStatus &&
      distribution === dragContext.distribution
    ) {
      return;
    };

    const response = await updateAttendeeStatus(dragContext.attendeeId, this.distribution, this.status);
    if (!response.ok) {
      const validationError = response.error.getValidationError();
      if (validationError === undefined) {
        throw new Error("Unexpected validation error", { cause: response.error });
      }
      dialogFactory().fromHtml(`<p>${validationError.message}</p>`).asAlert();
      return;
    }

    const attendeeList = this.querySelector<HTMLElement>(".attendeeList");
    const attendee = document.getElementById(dragContext.id)!;
    attendee.setAttribute("distribution", this.distribution);
    attendeeList?.insertAdjacentElement("beforeend", attendee);

    showNotification();
    DragContext.clear();
  }

  @Autobind
  dragLeaveHandler(event: DragEvent): void {
    if (!event.dataTransfer || event.dataTransfer.effectAllowed !== "move") return;
    event.preventDefault();

    this.classList.remove("selected");
  }

  get distribution(): string {
    return this.getAttribute("distribution")!;
  }

  get droppable(): string {
    return this.getAttribute("droppable")!;
  }

  get status(): number {
    return parseInt(this.getAttribute("status")!);
  }
}

window.customElements.define("mdrp-attendee-drag-and-drop-box", MDRPAttendeeDragAndDropBoxElement);

export default MDRPAttendeeDragAndDropBoxElement;
