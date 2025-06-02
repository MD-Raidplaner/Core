/**
 * Updates a attendee.
 *
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International
 */
define(["require", "exports", "WoltLabSuite/Core/Ajax/Backend", "WoltLabSuite/Core/Api/Result"], function (require, exports, Backend_1, Result_1) {
    "use strict";
    Object.defineProperty(exports, "__esModule", { value: true });
    exports.updateAttendeeStatus = updateAttendeeStatus;
    async function updateAttendeeStatus(attendeeId, distribution, status) {
        try {
            await (0, Backend_1.prepareRequest)(`${window.WSC_RPC_API_URL}rp/attendees/${attendeeId}/updateStatus`)
                .post({
                distribution,
                status,
            })
                .fetchAsJson();
        }
        catch (e) {
            return (0, Result_1.apiResultFromError)(e);
        }
        return (0, Result_1.apiResultFromValue)([]);
    }
});
