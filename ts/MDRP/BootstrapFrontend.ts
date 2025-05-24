/**
 * Bootstraps RP's JavaScript with additions for the frontend usage.
 *
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International
 */

import { whenFirstSeen } from "WoltLabSuite/Core/LazyLoader";
import { getCharacterPopover } from "./Api/Character/GetCharacterPopover";

export function setup(options: BootstrapOptions): void {
  window.RP_API_URL = options.RP_API_URL;

  setupCharacterPopover();

  whenFirstSeen("mdrp-attendee-drag-and-drop-box", () => {
    void import("./Component/Attendee/DragAndDrop/mdrp-attendee-drag-and-drop-box");
  });
  whenFirstSeen("mdrp-attendee-drag-and-drop-item", () => {
    void import("./Component/Attendee/DragAndDrop/mdrp-attendee-drag-and-drop-item");
  });
}

function setupCharacterPopover(): void {
  whenFirstSeen(".rpCharacterLink", () => {
    void import("WoltLabSuite/Core/Component/Popover").then(({ setupFor }) => {
      setupFor({
        endpoint: async (objectId: number) => {
          return (await getCharacterPopover(objectId)).unwrap();
        },
        identifier: "de.md-raidplaner.rp.character",
        selector: ".rpCharacterLink",
      });
    });
  });
}

declare global {
  interface Window {
    RP_API_URL: string;
  }
}

interface BootstrapOptions {
  RP_API_URL: string;
}
