/**
 * An abstract action, to handle character actions.
 *
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */

export abstract class AbstractCharacterAction {
  protected readonly button: HTMLElement;
  protected readonly characterDataElement: HTMLElement;
  protected readonly characterId: number;

  constructor(button: HTMLElement, characterId: number, characterDataElement: HTMLElement) {
    this.button = button;
    this.characterDataElement = characterDataElement;
    this.characterId = characterId;

    this.executeAction();
  }

  abstract executeAction(): void;
}

export default AbstractCharacterAction;
