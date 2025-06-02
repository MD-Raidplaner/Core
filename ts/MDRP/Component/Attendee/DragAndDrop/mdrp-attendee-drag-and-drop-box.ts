/**
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International
 */

import { Autobind } from "../../../Ui/Event/Raid/Participant/DragAndDrop/Autobind";
import { dialogFactory } from "WoltLabSuite/Core/Component/Dialog";
import { updateAttendeeStatus } from "../../../Api/Attendees/UpdateAttendeeStatus";
import { show as showNotification } from "WoltLabSuite/Core/Ui/Notification";

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
    if (!event.dataTransfer || event.dataTransfer.effectAllowed !== "move") return;
    event.preventDefault();

    const droppable = this.droppable;
    const droppableTo = event.dataTransfer.getData("droppableTo");
    if (!droppableTo.includes(droppable)) return;

    this.classList.add("selected");
  }

  @Autobind
  async dropHandler(event: DragEvent): Promise<void> {
    if (!event.dataTransfer || event.dataTransfer.effectAllowed !== "move") return;
    event.preventDefault();

    const droppable = this.droppable;
    const droppableTo = event.dataTransfer.getData("droppableTo");
    if (!droppableTo.includes(droppable)) return;

    const distribution = this.distribution;
    const status = this.status;

    if (
      status === event.dataTransfer.getData("currentStatus") &&
      distribution === event.dataTransfer.getData("distribution")
    ) {
      return;
    }

    const attendeeId = parseInt(event.dataTransfer.getData("attendeeId"));

    const response = await updateAttendeeStatus(attendeeId, this.distribution, this.status);
    if (!response.ok) {
      const validationError = response.error.getValidationError();
      if (validationError === undefined) {
        throw new Error("Unexpected validation error", { cause: response.error });
      }
      dialogFactory().fromHtml(`<p>${validationError.message}</p>`).asAlert();
      return;
    }

    const attendeeList = this.querySelector<HTMLElement>(".attendeeList");
    const attendee = document.getElementById(event.dataTransfer.getData("id"))!;
    attendee.setAttribute("distribution", this.distribution);
    attendeeList?.insertAdjacentElement("beforeend", attendee);

    showNotification();
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

  get status(): string {
    return this.getAttribute("status")!;
  }
}

window.customElements.define("mdrp-attendee-drag-and-drop-box", MDRPAttendeeDragAndDropBoxElement);

export default MDRPAttendeeDragAndDropBoxElement;
