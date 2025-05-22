/**
 * Handle other characters these Users by selection.
 *
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International
 */
define(["require", "exports"], function (require, exports) {
    "use strict";
    Object.defineProperty(exports, "__esModule", { value: true });
    exports.setup = setup;
    let _element;
    function setup(elementId) {
        _element = document.getElementById(elementId);
        _element.querySelectorAll("input").forEach((input) => {
            input.addEventListener("change", () => _change(input));
        });
    }
    function _change(input) {
        const userId = parseInt(input.dataset.userId);
        const value = input.value;
        const checked = input.checked;
        _element.querySelectorAll("input").forEach((inputElement) => {
            if (userId === parseInt(inputElement.dataset.userId) && value !== inputElement.value) {
                if (checked)
                    inputElement.disabled = true;
                else
                    inputElement.disabled = false;
            }
        });
    }
});
