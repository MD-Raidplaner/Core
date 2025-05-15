/**
 * An abstract action, to handle character actions.
 *
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */

import AbstractCharacterAction from "./Abstract";
import { dboAction } from "WoltLabSuite/Core/Ajax";
import { fire as fireHandler } from "WoltLabSuite/Core/Event/Handler";
import { show as showNotification } from "WoltLabSuite/Core/Ui/Notification";
import { stringToBool } from "WoltLabSuite/Core/Core";

export class DisableAction extends AbstractCharacterAction {
  executeAction(): void {
    this.button.addEventListener("click", (event) => void this.#click(event));
  }

  async #click(event: Event): Promise<void> {
    event.preventDefault();

    const isEnabled = stringToBool(this.characterDataElement.dataset.enabled!);

    await dboAction(isEnabled ? "disable" : "enable", "rp\\data\\character\\CharacterAction")
      .objectIds([this.characterId])
      .dispatch();

    this.characterDataElement.dataset.enabled = isEnabled ? "false" : "true";

    if (isEnabled) {
      this.button.textContent = this.button.dataset.enableMessage!;
    } else {
      this.button.textContent = this.button.dataset.disableMessage!;
    }

    showNotification();

    fireHandler("de.md-raidplaner.rp.acp.character", "refresh", {
      characterIds: [this.characterId],
    });
  }
}

export default DisableAction;
