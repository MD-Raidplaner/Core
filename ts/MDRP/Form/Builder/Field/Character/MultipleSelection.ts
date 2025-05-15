/**
 * Handle other characters these Users by selection.
 *
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International
 */


let _element: HTMLElement;

export function setup(elementId: string): void {
  _element = document.getElementById(elementId) as HTMLElement;
    _element.querySelectorAll("input").forEach((input: HTMLInputElement) => {
      input.addEventListener("change", () => _change(input));
    });
}

function _change(input: HTMLInputElement): void {
  const userId = parseInt(input.dataset.userId!);
  const value = input.value;
  const checked = input.checked;

  _element.querySelectorAll("input").forEach((inputElement: HTMLInputElement) => {
    if (userId === parseInt(inputElement.dataset.userId!) && value !== inputElement.value) {
      if (checked) inputElement.disabled = true;
      else inputElement.disabled = false;
    }
  });
}