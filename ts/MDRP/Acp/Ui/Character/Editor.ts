/**
 * User editing capabilities for the character list.
 *
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International
 */

import { add as addHandler } from "WoltLabSuite/Core/Event/Handler";
import { dboAction } from "WoltLabSuite/Core/Ajax";
import DeleteAction from "./Action/DeleteAction";
import DisableAction from "./Action/DisableAction";
import { getPhrase } from "WoltLabSuite/Core/Language";
import { show as showNotification } from "WoltLabSuite/Core/Ui/Notification";
import { stringToBool } from "WoltLabSuite/Core/Core";
import UiDropdownSimple from "WoltLabSuite/Core/Ui/Dropdown/Simple";
import { unmark as unmarkClipboard } from "WoltLabSuite/Core/Controller/Clipboard";

export class AcpUiCharacterEditor {
  /**
   * Initializes the edit dropdown for each character.
   */
  constructor() {
    document.querySelectorAll(".jsCharacterRow").forEach((characterRow: HTMLTableRowElement) => {
      this.#initCharacter(characterRow);
    });

    this.#initHandler();
  }

  async #clipboardAction(actionData: ClipboardActionData): Promise<void> {
    const characterIds: number[] = Object.values(actionData.data.parameters.objectIDs);

    if (actionData.data.actionName === "de.md-raidplaner.rp.character.enable") {
      await dboAction("enable", "rp\\data\\character\\CharacterAction").objectIds(characterIds).dispatch();

      Array.from(this.#getCharacterRows(characterIds)).forEach((characterRow) => {
        characterRow.dataset.enabled = "true";

        const characterId = ~~characterRow.dataset.objectId!;
        const dropdownId = `characterListDropdown${characterId}`;
        const dropdownMenu = UiDropdownSimple.getDropdownMenu(dropdownId)!;

        const enableCharacter = dropdownMenu.querySelector<HTMLAnchorElement>(".jsEnable");
        if (enableCharacter !== null) {
          enableCharacter.textContent = enableCharacter.dataset.disableMessage!;
        }
      });

      showNotification();

      this.#refreshCharacters({
        characterIds: characterIds,
      });

      unmarkClipboard("de.md-raidplaner.rp.character", characterIds);
    }
  }

  #getCharacterRows(characterIds: number[]): HTMLTableRowElement[] {
    const rows: HTMLTableRowElement[] = [];

    document.querySelectorAll(".jsCharacterRow").forEach((characterRow: HTMLTableRowElement) => {
      const characterId = ~~characterRow.dataset.objectId!;
      if (characterIds.includes(characterId)) {
        rows.push(characterRow);
      }
    });

    return rows;
  }

  #initCharacter(characterRow: HTMLTableRowElement): void {
    const characterId = ~~characterRow.dataset.objectId!;
    const dropdownId = `characterListDropdown${characterId}`;
    const dropdownMenu = UiDropdownSimple.getDropdownMenu(dropdownId)!;

    if (dropdownMenu.childElementCount === 0) {
      const toggleButton = characterRow.querySelector<HTMLAnchorElement>(".dropdownToggle");
      toggleButton?.classList.add("disabled");

      return;
    }

    const editLink = dropdownMenu.querySelector<HTMLAnchorElement>(".jsEditLink");
    if (editLink !== null) {
      const toggleButton = characterRow.querySelector<HTMLAnchorElement>(".dropdownToggle");
      toggleButton?.addEventListener("dblclick", (event) => {
        event.preventDefault();

        editLink.click();
      });
    }

    const enableCharacter = dropdownMenu.querySelector(".jsEnable");
    if (enableCharacter !== null) {
      new DisableAction(enableCharacter as HTMLAnchorElement, characterId, characterRow);
    }

    const deleteCharacter = dropdownMenu.querySelector(".jsDelete");
    if (deleteCharacter !== null) {
      new DeleteAction(deleteCharacter as HTMLAnchorElement, characterId, characterRow);
    }
  }

  #initHandler(): void {
    addHandler("de.md-raidplaner.rp.acp.character", "refresh", (data: RefreshCharactersData) => {
      this.#refreshCharacters(data);
    });

    addHandler("com.woltlab.wcf.clipboard", "de.md-raidplaner.rp.character", (data: ClipboardActionData) => {
      void this.#clipboardAction(data);
    });
  }

  #refreshCharacters(data: RefreshCharactersData): void {
    document.querySelectorAll(".jsCharacterRow").forEach((characterRow: HTMLTableRowElement) => {
      const characterId = ~~characterRow.dataset.objectId!;
      if (data.characterIds.includes(characterId)) {
        const characterStatusIcons = characterRow.querySelector<HTMLElement>(".characterStatusIcons")!;

        const isDisabled = !stringToBool(characterRow.dataset.enabled!);
        let iconIsDisabled = characterRow.querySelector<HTMLElement>(".jsCharacterIsDisabled");
        if (isDisabled && iconIsDisabled === null) {
          iconIsDisabled = document.createElement("span");
          iconIsDisabled.innerHTML = '<fa-icon name="power-off"></fa-icon>';
          iconIsDisabled.classList.add("jsCharacterIsDisabled", "jsTooltip");
          iconIsDisabled.title = getPhrase("rp.acp.character.isDisabled");
          characterStatusIcons.appendChild(iconIsDisabled);
        } else {
          iconIsDisabled!.remove();
        }
      }
    });
  }
}

export default AcpUiCharacterEditor;

interface ClipboardActionData {
  data: {
    actionName: "de.md-raidplaner.rp.character.delete" | "de.md-raidplaner.rp.character.enable";
    parameters: {
      objectIDs: ClipboardObjectIDsData;
    };
  };
  responseData: null;
}

type ClipboardObjectIDsData = Record<number, number>;

interface RefreshCharactersData {
  characterIds: number[];
}
