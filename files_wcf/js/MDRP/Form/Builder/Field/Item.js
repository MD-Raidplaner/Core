/**
 * Manages the item entered form field.
 *
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International
 */
define(["require", "exports", "tslib", "WoltLabSuite/Core/Language", "WoltLabSuite/Core/Dom/Traverse", "WoltLabSuite/Core/Dom/Util", "../../../Api/Items/SearchItem", "WoltLabSuite/Core/Component/Confirmation", "../../../Api/Items/Tooltip"], function (require, exports, tslib_1, Language_1, Traverse_1, Util_1, SearchItem_1, Confirmation_1, Tooltip_1) {
    "use strict";
    var _a;
    Object.defineProperty(exports, "__esModule", { value: true });
    exports.Item = void 0;
    Util_1 = tslib_1.__importDefault(Util_1);
    class Item {
        #addButton;
        #itemCharacter;
        #form;
        #formFieldId;
        #itemAdditional;
        #itemList;
        #itemName;
        #itemPointAccount;
        #itemPoints;
        static #pointsRegExp = new RegExp(/^(?=.)([+-]?([0-9]*)(\.([0-9]+))?)$/);
        constructor(formFieldId, existingItems) {
            this.#formFieldId = formFieldId;
            this.#itemList = document.getElementById(`${this.#formFieldId}_itemList`);
            if (this.#itemList === null) {
                throw new Error(`Cannot find item list for items field with id '${this.#formFieldId}'.`);
            }
            this.#itemName = document.getElementById(`${this.#formFieldId}_itemName`);
            if (this.#itemName === null) {
                throw new Error(`Cannot find item name for items field with id '${this.#formFieldId}'.`);
            }
            this.#itemAdditional = document.getElementById(`${this.#formFieldId}_additionalData`);
            if (this.#itemAdditional === null) {
                throw new Error(`Cannot find item additional data for items field with id '${this.#formFieldId}'.`);
            }
            this.#itemPointAccount = document.getElementById(`${this.#formFieldId}_pointAccount`);
            if (this.#itemPointAccount === null) {
                throw new Error(`Cannot find item point account for items field with id '${this.#formFieldId}'.`);
            }
            this.#itemCharacter = document.getElementById(`${this.#formFieldId}_character`);
            if (this.#itemCharacter === null) {
                throw new Error(`Cannot find item character for items field with id '${this.#formFieldId}'.`);
            }
            this.#itemPoints = document.getElementById(`${this.#formFieldId}_points`);
            if (this.#itemPoints === null) {
                throw new Error(`Cannot find item points for items field with id '${this.#formFieldId}'.`);
            }
            this.#addButton = document.getElementById(`${this.#formFieldId}_addButton`);
            if (this.#addButton === null) {
                throw new Error(`Cannot find add button for items field with id '${this.#formFieldId}'.`);
            }
            this.#addButton.addEventListener("click", () => void this.#addItem());
            this.#form = this.#itemList.closest("form");
            if (this.#form === null) {
                throw new Error(`Cannot find form element for items field with id '${this.#formFieldId}'.`);
            }
            this.#form.addEventListener("submit", () => this.#submit());
            existingItems.forEach((data) => void this.#addItemByData(data));
        }
        /**
         * Adds a set of item.
         *
         * If the item data is invalid, an error message is shown and no item set is added.
         */
        async #addItem() {
            if (!this.#validateInput()) {
                return;
            }
            const response = await (0, SearchItem_1.searchItem)(this.#itemName.value, this.#itemAdditional.value);
            if (!response.ok) {
                const validationError = response.error.getValidationError();
                if (validationError === undefined) {
                    throw new Error("Unexpected validation error", { cause: response.error });
                }
                return;
            }
            const characterName = this.#itemCharacter.selectedOptions[0]?.textContent || "";
            const pointAccountName = this.#itemPointAccount.selectedOptions[0]?.textContent || "";
            const item = {
                additionalData: this.#itemAdditional.value,
                characterId: parseInt(this.#itemCharacter.value),
                characterName: characterName,
                itemId: response.value.itemID,
                itemName: response.value.itemName,
                pointAccountId: parseInt(this.#itemPointAccount.value),
                pointAccountName: pointAccountName,
                points: parseFloat(this.#itemPoints.value),
            };
            void this.#addItemByData(item);
            this.#emptyInput();
            this.#itemName.focus();
        }
        /**
         * Adds a item to the item list using the given item data.
         */
        async #addItemByData(itemData) {
            const { tooltip } = (await (0, Tooltip_1.itemTooltip)(itemData.itemId)).unwrap();
            // add item to list
            const item = document.createElement("div");
            item.classList.add("rpItem");
            item.dataset.itemAdditional = itemData.additionalData;
            item.dataset.itemCharacterId = itemData.characterId.toString();
            item.dataset.itemCharacterName = itemData.characterName;
            item.dataset.itemId = itemData.itemId.toString();
            item.dataset.itemName = itemData.itemName;
            item.dataset.itemPointAccountId = itemData.pointAccountId.toString();
            item.dataset.itemPointAccountName = itemData.pointAccountName;
            item.dataset.itemPoints = itemData.points.toString();
            item.innerHTML = ` ${(0, Language_1.getPhrase)("rp.item.form.field", {
                itemName: itemData.itemName,
                pointAccountName: itemData.pointAccountName,
                characterName: itemData.characterName,
                points: itemData.points,
                tooltip: tooltip,
            })}`;
            // add delete button
            const deleteButton = document.createElement("button");
            deleteButton.type = "button";
            deleteButton.innerHTML = '<fa-icon size="16" name="times"></fa-icon>';
            deleteButton.title = (0, Language_1.getPhrase)("wcf.global.button.delete");
            deleteButton.classList.add("jsTooltip");
            deleteButton.addEventListener("click", (ev) => this.#removeItem(ev));
            item.insertAdjacentElement("afterbegin", deleteButton);
            this.#itemList.appendChild(item);
        }
        /**
         * Empties the input fields.
         */
        #emptyInput() {
            this.#itemAdditional.value = "";
            this.#itemName.value = "";
            this.#itemPointAccount.options.selectedIndex = 0;
            this.#itemCharacter.options.selectedIndex = 0;
            this.#itemPoints.value = "";
        }
        async #removeItem(event) {
            const item = event.currentTarget.closest("div");
            const result = await (0, Confirmation_1.confirmationFactory)().delete();
            if (result) {
                item?.remove();
            }
        }
        /**
         * Adds all necessary (hidden) form fields to the form when
         * submitting the form.
         */
        #submit() {
            (0, Traverse_1.childrenByTag)(this.#itemList, "DIV").forEach((item, index) => {
                const itemAdditional = document.createElement("input");
                itemAdditional.type = "hidden";
                itemAdditional.name = `${this.#formFieldId}[${index}][additionalData]`;
                itemAdditional.value = item.dataset.itemAdditional;
                this.#form.appendChild(itemAdditional);
                const itemCharacterID = document.createElement("input");
                itemCharacterID.type = "hidden";
                itemCharacterID.name = `${this.#formFieldId}[${index}][characterID]`;
                itemCharacterID.value = item.dataset.itemCharacterId;
                this.#form.appendChild(itemCharacterID);
                const itemCharacterName = document.createElement("input");
                itemCharacterName.type = "hidden";
                itemCharacterName.name = `${this.#formFieldId}[${index}][characterName]`;
                itemCharacterName.value = item.dataset.itemCharacterName;
                this.#form.appendChild(itemCharacterName);
                const itemID = document.createElement("input");
                itemID.type = "hidden";
                itemID.name = `${this.#formFieldId}[${index}][itemID]`;
                itemID.value = item.dataset.itemId;
                this.#form.appendChild(itemID);
                const itemName = document.createElement("input");
                itemName.type = "hidden";
                itemName.name = `${this.#formFieldId}[${index}][itemName]`;
                itemName.value = item.dataset.itemName;
                this.#form.appendChild(itemName);
                const itemPointAccountID = document.createElement("input");
                itemPointAccountID.type = "hidden";
                itemPointAccountID.name = `${this.#formFieldId}[${index}][pointAccountID]`;
                itemPointAccountID.value = item.dataset.itemPointAccountId;
                this.#form.appendChild(itemPointAccountID);
                const itemPointAccountName = document.createElement("input");
                itemPointAccountName.type = "hidden";
                itemPointAccountName.name = `${this.#formFieldId}[${index}][pointAccountName]`;
                itemPointAccountName.value = item.dataset.itemPointAccountName;
                this.#form.appendChild(itemPointAccountName);
                const itemPoints = document.createElement("input");
                itemPoints.type = "hidden";
                itemPoints.name = `${this.#formFieldId}[${index}][points]`;
                itemPoints.value = item.dataset.itemPoints;
                this.#form.appendChild(itemPoints);
            });
        }
        /**
         * Returns `true` if the currently entered item data is valid.
         * Otherwise `false` is returned and relevant error messages are
         * shown.
         */
        #validateInput() {
            return this.#validateItemName() && this.#validateItemPoints();
            // TODO Add Validate for character and pointAccount
        }
        /**
         * Returns `true` if the currently entered item name is
         * valid. Otherwise `false` is returned and an error message is
         * shown.
         */
        #validateItemName() {
            const itemName = this.#itemName.value;
            if (itemName === "") {
                Util_1.default.innerError(this.#itemName, (0, Language_1.getPhrase)("wcf.global.form.error.empty"));
                return false;
            }
            // check if item has already been added
            const duplicate = (0, Traverse_1.childrenByTag)(this.#itemList, "div").some((item) => {
                return item.dataset.itemName === itemName && item.dataset.itemCharacterId === this.#itemCharacter.value;
            });
            if (duplicate) {
                Util_1.default.innerError(this.#itemName, (0, Language_1.getPhrase)("rp.item.error.duplicate"));
                return false;
            }
            // remove outdated errors
            Util_1.default.innerError(this.#itemName, "");
            return true;
        }
        /**
         * Returns `true` if the currently entered item points is
         * valid. Otherwise `false` is returned and an error message is
         * shown.
         */
        #validateItemPoints() {
            const itemPoints = this.#itemPoints.value;
            if (itemPoints === "") {
                Util_1.default.innerError(this.#itemPoints, (0, Language_1.getPhrase)("wcf.global.form.error.empty"));
                return false;
            }
            if (!_a.#pointsRegExp.test(itemPoints)) {
                Util_1.default.innerError(this.#itemPoints, (0, Language_1.getPhrase)("rp.item.points.error.format"));
                return false;
            }
            // remove outdated errors
            Util_1.default.innerError(this.#itemPoints, "");
            return true;
        }
    }
    exports.Item = Item;
    _a = Item;
});
