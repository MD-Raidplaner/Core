/**
 * Provides suggestions for characters.
 *
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International
 */
define(["require", "exports", "tslib", "WoltLabSuite/Core/Core", "WoltLabSuite/Core/Ui/Search/Input"], function (require, exports, tslib_1, Core, Input_1) {
    "use strict";
    Object.defineProperty(exports, "__esModule", { value: true });
    exports.UiCharacterSearchInput = void 0;
    Core = tslib_1.__importStar(Core);
    Input_1 = tslib_1.__importDefault(Input_1);
    class UiCharacterSearchInput extends Input_1.default {
        constructor(element, options) {
            options = Core.extend({
                ajax: {
                    className: "rp\\data\\character\\CharacterAction",
                },
            }, options);
            super(element, options);
        }
        createListItem(item) {
            const listItem = super.createListItem(item);
            const box = document.createElement("div");
            box.className = "box16";
            box.innerHTML = item.icon;
            box.appendChild(listItem.children[0]);
            listItem.appendChild(box);
            return listItem;
        }
    }
    exports.UiCharacterSearchInput = UiCharacterSearchInput;
    exports.default = UiCharacterSearchInput;
});
