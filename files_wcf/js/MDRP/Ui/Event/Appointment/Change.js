/**
 * Provides participation in events.
 *
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International
 */
define(["require", "exports", "tslib", "WoltLabSuite/Core/Ajax", "WoltLabSuite/Core/Component/Dialog", "WoltLabSuite/Core/Dom/Util", "WoltLabSuite/Core/Language", "WoltLabSuite/Core/Ui/Notification", "WoltLabSuite/Core/User"], function (require, exports, tslib_1, Ajax_1, Dialog_1, Util_1, Language_1, Notification_1, User_1) {
    "use strict";
    Object.defineProperty(exports, "__esModule", { value: true });
    exports.UiEventAppointmentChange = void 0;
    Util_1 = tslib_1.__importDefault(Util_1);
    User_1 = tslib_1.__importDefault(User_1);
    class UiEventAppointmentChange {
        #dialog;
        #eventId;
        constructor(button) {
            this.#eventId = ~~button.dataset.eventId;
            button.addEventListener("click", () => {
                this.#show();
            });
        }
        #createDialog() {
            const dialog = (0, Dialog_1.dialogFactory)().fromId("appointmentChangeDialog").asPrompt();
            const content = dialog.content;
            dialog.addEventListener("primary", () => {
                const status = content.querySelector('input[name="status"]:checked');
                void (async () => {
                    const response = (await (0, Ajax_1.dboAction)("appointmentSetStatus", "rp\\data\\event\\EventAction")
                        .payload({
                        eventID: this.#eventId,
                        status: status.value,
                    })
                        .disableLoadingIndicator()
                        .dispatch());
                    document.querySelectorAll(".jsEventAppointment").forEach((appointment) => {
                        const appointmentStatus = appointment.dataset.status;
                        const element = appointment.querySelector(`li[data-object-id="${User_1.default.userId}"]`);
                        if (element && appointmentStatus !== status.value) {
                            element.remove();
                        }
                        else if (!element && appointmentStatus === status.value) {
                            const notice = appointment.querySelector("woltlab-core-notice");
                            if (notice) {
                                appointment.innerHTML = "";
                            }
                            Util_1.default.insertHtml(response.template, appointment, "prepend");
                        }
                    });
                    (0, Notification_1.show)();
                })();
            });
            return dialog;
        }
        #show() {
            if (!this.#dialog) {
                this.#dialog = this.#createDialog();
            }
            this.#dialog.show((0, Language_1.getPhrase)("rp.event.participation"));
        }
    }
    exports.UiEventAppointmentChange = UiEventAppointmentChange;
    exports.default = UiEventAppointmentChange;
});
