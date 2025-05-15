/**
 * Deletes a attendee.
 *
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International
 */

import { prepareRequest } from "WoltLabSuite/Core/Ajax/Backend";
import { ApiResult, apiResultFromError, apiResultFromValue } from "WoltLabSuite/Core/Api/Result";

export async function deleteAttendee(attendeeId: number): Promise<ApiResult<[]>> {
  const url = new URL(`${window.WSC_RPC_API_URL}rp/attendees/${attendeeId}`);

  try {
    await prepareRequest(url).delete().fetchAsJson();
  } catch (e) {
    return apiResultFromError(e);
  }

  return apiResultFromValue([]);
}
