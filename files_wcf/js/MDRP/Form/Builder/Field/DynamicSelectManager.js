/**
 * A class for dynamically managing the visibility and activation of options
 * in a target select element based on the selection of a current select element.
 *
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International
 */
define(["require", "exports"], function (require, exports) {
    "use strict";
    Object.defineProperty(exports, "__esModule", { value: true });
    exports.DynamicSelectManager = void 0;
    class DynamicSelectManager {
        #triggerSelect;
        #filteredSelect;
        #optionsMapping;
        constructor(triggerSelect, filteredSelect, optionsMapping) {
            this.#triggerSelect = document.getElementById(triggerSelect);
            this.#filteredSelect = document.getElementById(filteredSelect);
            this.#optionsMapping = optionsMapping;
            const selectedValue = this.#filteredSelect.value;
            this.#triggerSelect.addEventListener("change", () => this.#handleSelectChange());
            this.#handleSelectChange(selectedValue);
        }
        #handleSelectChange(selectedValue) {
            const triggerValue = this.#triggerSelect.value;
            const allowedOptions = this.#optionsMapping[triggerValue] || [];
            Array.from(this.#filteredSelect.options).forEach((option) => {
                const optionValue = option.value;
                if (allowedOptions.includes(optionValue) || !optionValue) {
                    option.style.display = "block";
                    option.disabled = false;
                    if (selectedValue && optionValue === selectedValue) {
                        option.selected = true;
                    }
                }
                else {
                    option.style.display = "none";
                    option.disabled = true;
                }
            });
            if (!selectedValue) {
                this.#filteredSelect.selectedIndex = 0;
            }
        }
    }
    exports.DynamicSelectManager = DynamicSelectManager;
});
