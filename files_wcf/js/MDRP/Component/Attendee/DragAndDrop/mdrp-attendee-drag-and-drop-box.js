/**
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International
 */
define(["require", "exports", "tslib", "./Autobind", "WoltLabSuite/Core/Component/Dialog", "../../../Api/Attendees/UpdateAttendeeStatus", "WoltLabSuite/Core/Ui/Notification", "./DragContext"], function (require, exports, tslib_1, Autobind_1, Dialog_1, UpdateAttendeeStatus_1, Notification_1, DragContext_1) {
    "use strict";
    Object.defineProperty(exports, "__esModule", { value: true });
    exports.MDRPAttendeeDragAndDropBoxElement = void 0;
    class MDRPAttendeeDragAndDropBoxElement extends HTMLElement {
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
        dragOverHandler(event) {
            event.preventDefault();
            if (!event.dataTransfer || event.dataTransfer.effectAllowed !== "move")
                return;
            const dragContext = DragContext_1.DragContext.get();
            if (!dragContext) {
                console.warn("DragContext is not set, cannot handle drag over event.");
                return;
            }
            const droppable = this.droppable;
            const droppableTo = dragContext.droppableTo;
            if (!droppableTo.includes(droppable))
                return;
            this.classList.add("selected");
        }
        async dropHandler(event) {
            if (!event.dataTransfer || event.dataTransfer.effectAllowed !== "move")
                return;
            event.preventDefault();
            const dragContext = DragContext_1.DragContext.get();
            if (!dragContext) {
                console.warn("DragContext is not set, cannot handle drop event.");
                return;
            }
            const droppable = this.droppable;
            const droppableTo = dragContext.droppableTo;
            if (!droppableTo.includes(droppable))
                return;
            const distribution = this.distribution;
            const status = this.status;
            if (status === dragContext.currentStatus &&
                distribution === dragContext.distribution) {
                return;
            }
            ;
            const response = await (0, UpdateAttendeeStatus_1.updateAttendeeStatus)(dragContext.attendeeId, this.distribution, this.status);
            if (!response.ok) {
                const validationError = response.error.getValidationError();
                if (validationError === undefined) {
                    throw new Error("Unexpected validation error", { cause: response.error });
                }
                (0, Dialog_1.dialogFactory)().fromHtml(`<p>${validationError.message}</p>`).asAlert();
                return;
            }
            const attendeeList = this.querySelector(".attendeeList");
            const attendee = document.getElementById(dragContext.id);
            attendee.setAttribute("distribution", this.distribution);
            attendeeList?.insertAdjacentElement("beforeend", attendee);
            (0, Notification_1.show)();
            DragContext_1.DragContext.clear();
        }
        dragLeaveHandler(event) {
            if (!event.dataTransfer || event.dataTransfer.effectAllowed !== "move")
                return;
            event.preventDefault();
            this.classList.remove("selected");
        }
        get distribution() {
            return this.getAttribute("distribution");
        }
        get droppable() {
            return this.getAttribute("droppable");
        }
        get status() {
            return parseInt(this.getAttribute("status"));
        }
    }
    exports.MDRPAttendeeDragAndDropBoxElement = MDRPAttendeeDragAndDropBoxElement;
    tslib_1.__decorate([
        Autobind_1.Autobind,
        tslib_1.__metadata("design:type", Function),
        tslib_1.__metadata("design:paramtypes", [DragEvent]),
        tslib_1.__metadata("design:returntype", void 0)
    ], MDRPAttendeeDragAndDropBoxElement.prototype, "dragOverHandler", null);
    tslib_1.__decorate([
        Autobind_1.Autobind,
        tslib_1.__metadata("design:type", Function),
        tslib_1.__metadata("design:paramtypes", [DragEvent]),
        tslib_1.__metadata("design:returntype", Promise)
    ], MDRPAttendeeDragAndDropBoxElement.prototype, "dropHandler", null);
    tslib_1.__decorate([
        Autobind_1.Autobind,
        tslib_1.__metadata("design:type", Function),
        tslib_1.__metadata("design:paramtypes", [DragEvent]),
        tslib_1.__metadata("design:returntype", void 0)
    ], MDRPAttendeeDragAndDropBoxElement.prototype, "dragLeaveHandler", null);
    window.customElements.define("mdrp-attendee-drag-and-drop-box", MDRPAttendeeDragAndDropBoxElement);
    exports.default = MDRPAttendeeDragAndDropBoxElement;
});
