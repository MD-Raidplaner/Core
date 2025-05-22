/**
 * Bootstraps RP's JavaScript with additions for the frontend usage.
 *
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International
 */
var __createBinding = (this && this.__createBinding) || (Object.create ? (function(o, m, k, k2) {
    if (k2 === undefined) k2 = k;
    var desc = Object.getOwnPropertyDescriptor(m, k);
    if (!desc || ("get" in desc ? !m.__esModule : desc.writable || desc.configurable)) {
      desc = { enumerable: true, get: function() { return m[k]; } };
    }
    Object.defineProperty(o, k2, desc);
}) : (function(o, m, k, k2) {
    if (k2 === undefined) k2 = k;
    o[k2] = m[k];
}));
var __setModuleDefault = (this && this.__setModuleDefault) || (Object.create ? (function(o, v) {
    Object.defineProperty(o, "default", { enumerable: true, value: v });
}) : function(o, v) {
    o["default"] = v;
});
var __importStar = (this && this.__importStar) || (function () {
    var ownKeys = function(o) {
        ownKeys = Object.getOwnPropertyNames || function (o) {
            var ar = [];
            for (var k in o) if (Object.prototype.hasOwnProperty.call(o, k)) ar[ar.length] = k;
            return ar;
        };
        return ownKeys(o);
    };
    return function (mod) {
        if (mod && mod.__esModule) return mod;
        var result = {};
        if (mod != null) for (var k = ownKeys(mod), i = 0; i < k.length; i++) if (k[i] !== "default") __createBinding(result, mod, k[i]);
        __setModuleDefault(result, mod);
        return result;
    };
})();
define(["require", "exports", "WoltLabSuite/Core/LazyLoader"], function (require, exports, LazyLoader_1) {
    "use strict";
    Object.defineProperty(exports, "__esModule", { value: true });
    exports.setup = setup;
    function setup(options) {
        window.RP_API_URL = options.RP_API_URL;
        setupCharacterPopover(options.endpointCharacterPopover);
        (0, LazyLoader_1.whenFirstSeen)("mdrp-attendee-drag-and-drop-box", () => {
            void new Promise((resolve_1, reject_1) => { require(["./Component/Attendee/DragAndDrop/mdrp-attendee-drag-and-drop-box"], resolve_1, reject_1); }).then(__importStar);
        });
        (0, LazyLoader_1.whenFirstSeen)("mdrp-attendee-drag-and-drop-item", () => {
            void new Promise((resolve_2, reject_2) => { require(["./Component/Attendee/DragAndDrop/mdrp-attendee-drag-and-drop-item"], resolve_2, reject_2); }).then(__importStar);
        });
    }
    function setupCharacterPopover(endpoint) {
        if (endpoint === "") {
            return;
        }
        (0, LazyLoader_1.whenFirstSeen)(".rpCharacterLink", () => {
            void new Promise((resolve_3, reject_3) => { require(["WoltLabSuite/Core/Component/Popover"], resolve_3, reject_3); }).then(__importStar).then(({ setupFor }) => {
                setupFor({
                    endpoint,
                    identifier: "de.md-raidplaner.rp.character",
                    selector: ".rpCharacterLink",
                });
            });
        });
    }
});
