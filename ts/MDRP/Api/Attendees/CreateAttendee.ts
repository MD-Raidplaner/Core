/**
 * Create a new attendee.
 *
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International
 */

import { prepareRequest } from "WoltLabSuite/Core/Ajax/Backend";
import { ApiResult, apiResultFromError, apiResultFromValue } from "WoltLabSuite/Core/Api/Result";

export async function createAttendee(
  eventId: number,
  characterId: string,
  role: string,
  status: number,
  guestToken: string = "",
): Promise<ApiResult<Response>> {
    const url = new URL(`${window.WSC_RPC_API_URL}rp/attendees`);

    const payload = {
        eventID: eventId,
        characterID: characterId,
        role: role,
        status,
        guestToken,
    };

    let response: Response;
    try {
        response = (await prepareRequest(url).post(payload).fetchAsJson()) as Response;
    } catch (e) {
        return apiResultFromError(e);
    }

    return apiResultFromValue(response);
}

type Response = {
  attendeeId: number;
};
