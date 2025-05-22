/**
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International
 */
define(["require", "exports", "tslib", "WoltLabSuite/Core/Core", "WoltLabSuite/Core/Language", "WoltLabSuite/Core/Component/Confirmation", "WoltLabSuite/Core/Ui/Notification", "../../Api/Events/TrashEvent", "WoltLabSuite/Core/Component/Dialog", "../../Api/Events/RestoreEvent", "../../Api/Events/EnableDisableEvent", "../../Api/Events/DeleteEvent", "WoltLabSuite/Core/Dom/Util", "../../Api/Events/CancelEvent"], function (require, exports, tslib_1, Core_1, Language_1, Confirmation_1, Notification_1, TrashEvent_1, Dialog_1, RestoreEvent_1, EnableDisableEvent_1, DeleteEvent_1, Util_1, CancelEvent_1) {
    "use strict";
    Object.defineProperty(exports, "__esModule", { value: true });
    exports.UiEventEditor = void 0;
    Util_1 = tslib_1.__importDefault(Util_1);
    class UiEventEditor {
        #elements = new Map();
        #event;
        #eventIcons;
        #eventId;
        constructor() {
            this.#event = document.querySelector(".event");
            this.#eventId = parseInt(this.#event.dataset.eventId);
            this.#eventIcons = document.querySelector(".rpEventHeader .contentHeaderTitle .contentTitle");
            this.#rebuild();
        }
        async #cancelExecute() {
            const result = await this.#cancelConfirmation();
            if (result) {
                const response = await (0, CancelEvent_1.cancelEvent)(this.#eventId);
                if (!response.ok) {
                    const validationError = response.error.getValidationError();
                    if (validationError === undefined) {
                        throw new Error("Unexpected validation error", { cause: response.error });
                    }
                    (0, Dialog_1.dialogFactory)().fromHtml(`<p>${validationError.message}</p>`).asAlert();
                    return;
                }
                (0, Notification_1.show)();
                window.location.reload();
            }
        }
        async #cancelConfirmation() {
            const title = this.#event.dataset.title;
            const question = (0, Language_1.getPhrase)("rp.event.raid.cancel.confirmMessage", { title });
            const dialog = (0, Dialog_1.dialogFactory)().withoutContent().asConfirmation();
            dialog.show(question);
            return new Promise((resolve) => {
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
        #click(optionName, event) {
            event.preventDefault();
            const element = this.#elements.get(optionName);
            if (optionName === "editLink" || optionName === "transform") {
                window.location.href = element.dataset.link;
            }
            else {
                this.#execute(optionName);
            }
        }
        async #deleteExecute() {
            const title = this.#event.dataset.title;
            const result = await (0, Confirmation_1.confirmationFactory)().delete(title);
            if (result) {
                const response = await (0, DeleteEvent_1.deleteEvent)(this.#eventId);
                if (!response.ok) {
                    const validationError = response.error.getValidationError();
                    if (validationError === undefined) {
                        throw new Error("Unexpected validation error", { cause: response.error });
                    }
                    (0, Dialog_1.dialogFactory)().fromHtml(`<p>${validationError.message}</p>`).asAlert();
                    return;
                }
                (0, Notification_1.show)();
                window.location.href = `${window.RP_API_URL}index.php?calendar`;
            }
        }
        async #enableDisableExecute() {
            const isEnabled = (0, Core_1.stringToBool)(this.#event.dataset.enabled);
            const response = await (0, EnableDisableEvent_1.enableDisableEvent)(this.#eventId, isEnabled);
            if (!response.ok) {
                const validationError = response.error.getValidationError();
                if (validationError === undefined) {
                    throw new Error("Unexpected validation error", { cause: response.error });
                }
                (0, Dialog_1.dialogFactory)().fromHtml(`<p>${validationError.message}</p>`).asAlert();
                return;
            }
            this.#event.dataset.enabled = isEnabled ? "false" : "true";
            const isDisabled = !(0, Core_1.stringToBool)(this.#event.dataset.enabled);
            let iconIsDisabled = document.querySelector(".rpEventHeader .jsIsDisabled");
            if (isDisabled && iconIsDisabled === null) {
                iconIsDisabled = document.createElement("span");
                iconIsDisabled.classList.add("badge", "label", "green", "jsIsDisabled");
                iconIsDisabled.innerHTML = (0, Language_1.getPhrase)("wcf.message.status.disabled");
                this.#eventIcons.appendChild(iconIsDisabled);
            }
            else if (!isDisabled && iconIsDisabled !== null) {
                iconIsDisabled.remove();
            }
            (0, Notification_1.show)();
            this.#rebuild();
        }
        #execute(optionName) {
            if (!this.#validate(optionName))
                return;
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
        #rebuild() {
            let showDropdown = false;
            document.querySelectorAll(".jsEventDropdownItems > li").forEach((element) => {
                const optionName = element.dataset.optionName;
                if (optionName) {
                    if (this.#validate(optionName)) {
                        Util_1.default.show(element);
                        showDropdown = true;
                    }
                    else {
                        Util_1.default.hide(element);
                    }
                    if (!this.#elements.get(optionName)) {
                        element.addEventListener("click", (ev) => this.#click(optionName, ev));
                        this.#elements.set(optionName, element);
                        if (optionName === "editLink") {
                            const dropdownToggle = document.querySelector(".jsEventDropdown > .dropdownToggle");
                            dropdownToggle?.addEventListener("dblclick", () => {
                                element.click();
                            });
                        }
                    }
                }
            });
            const dropdownMenu = document.querySelector(".jsEventDropdown");
            if (!showDropdown) {
                dropdownMenu.remove();
            }
            else {
                Util_1.default.show(dropdownMenu);
            }
        }
        async #restoreExecute() {
            const title = this.#event.dataset.title;
            const result = await (0, Confirmation_1.confirmationFactory)().restore(title);
            if (result) {
                const response = await (0, RestoreEvent_1.restoreEvent)(this.#eventId);
                if (!response.ok) {
                    const validationError = response.error.getValidationError();
                    if (validationError === undefined) {
                        throw new Error("Unexpected validation error", { cause: response.error });
                    }
                    (0, Dialog_1.dialogFactory)().fromHtml(`<p>${validationError.message}</p>`).asAlert();
                    return;
                }
                const iconIsDeleted = document.querySelector(".rpEventHeader .jsIsDeleted");
                if (iconIsDeleted !== null) {
                    iconIsDeleted.remove();
                }
                this.#event.dataset.deleted = "false";
                (0, Notification_1.show)();
                this.#rebuild();
            }
        }
        async #trashExecute() {
            const title = this.#event.dataset.title;
            const { result } = await (0, Confirmation_1.confirmationFactory)().softDelete(title);
            if (result) {
                const response = await (0, TrashEvent_1.trashEvent)(this.#eventId);
                if (!response.ok) {
                    const validationError = response.error.getValidationError();
                    if (validationError === undefined) {
                        throw new Error("Unexpected validation error", { cause: response.error });
                    }
                    (0, Dialog_1.dialogFactory)().fromHtml(`<p>${validationError.message}</p>`).asAlert();
                    return;
                }
                this.#event.dataset.deleted = "true";
                let iconIsDeleted = document.querySelector(".rpEventHeader .jsIsDeleted");
                if (iconIsDeleted === null) {
                    iconIsDeleted = document.createElement("span");
                    iconIsDeleted.classList.add("badge", "label", "red", "jsIsDeleted");
                    iconIsDeleted.innerHTML = (0, Language_1.getPhrase)("wcf.message.status.deleted");
                    this.#eventIcons.appendChild(iconIsDeleted);
                }
                (0, Notification_1.show)();
                this.#rebuild();
            }
        }
        #validate(optionName) {
            switch (optionName) {
                case "cancel":
                    if (!(0, Core_1.stringToBool)(this.#event.dataset.canCancel)) {
                        return false;
                    }
                    if (!(0, Core_1.stringToBool)(this.#event.dataset.canceled)) {
                        return true;
                    }
                    break;
                case "delete":
                    if (!(0, Core_1.stringToBool)(this.#event.dataset.canDelete)) {
                        return false;
                    }
                    if ((0, Core_1.stringToBool)(this.#event.dataset.deleted)) {
                        return true;
                    }
                    break;
                case "restore":
                    if (!(0, Core_1.stringToBool)(this.#event.dataset.canRestore)) {
                        return false;
                    }
                    if ((0, Core_1.stringToBool)(this.#event.dataset.deleted)) {
                        return true;
                    }
                    break;
                case "trash":
                    if (!(0, Core_1.stringToBool)(this.#event.dataset.canTrash)) {
                        return false;
                    }
                    if (!(0, Core_1.stringToBool)(this.#event.dataset.deleted)) {
                        return true;
                    }
                    break;
                case "enable":
                case "disable":
                    if (!(0, Core_1.stringToBool)(this.#event.dataset.canEdit)) {
                        return false;
                    }
                    if ((0, Core_1.stringToBool)(this.#event.dataset.canceled)) {
                        return false;
                    }
                    if ((0, Core_1.stringToBool)(this.#event.dataset.deleted)) {
                        return false;
                    }
                    if ((0, Core_1.stringToBool)(this.#event.dataset.enabled)) {
                        return optionName === "disable";
                    }
                    else {
                        return optionName === "enable";
                    }
                    break;
                case "editLink":
                    if ((0, Core_1.stringToBool)(this.#event.dataset.canEdit)) {
                        return true;
                    }
                    break;
                case "transform":
                    if ((0, Core_1.stringToBool)(this.#event.dataset.canTransform)) {
                        return true;
                    }
                    break;
            }
            return false;
        }
    }
    exports.UiEventEditor = UiEventEditor;
    exports.default = UiEventEditor;
});
