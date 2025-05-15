/**
 * Gets the html code for the rendering of a item tooltip.
 *
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International
 */

import { prepareRequest } from "WoltLabSuite/Core/Ajax/Backend";
import { ApiResult, apiResultFromError, apiResultFromValue } from "WoltLabSuite/Core/Api/Result";

export async function itemTooltip(itemId: number): Promise<ApiResult<Response>> {
  const url = new URL(`${window.WSC_RPC_API_URL}rp/items/${itemId}/tooltip`);

  let response: Response;
  try {
    response = (await prepareRequest(url).get().fetchAsJson()) as Response;
  } catch (e) {
    return apiResultFromError(e);
  }

  return apiResultFromValue(response);
}

type Response = {
    tooltip: string;
};
