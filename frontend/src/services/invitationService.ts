import axios from '@/plugins/axios';

export async function sendInvitation(id: string): Promise<{code: string}> {
  try {
    await axios.post(`/invitation/${id}/send`);

    return {
      code: 'SUCCESS'
    };
  } catch (error) {
    throw error;
  }
}

export async function acceptInvitation(id: string): Promise<{code: string}> {
  try {
    await axios.post(`/invitation/${id}/accept`);

    return {
      code: 'SUCCESS'
    };
  } catch (error) {
    throw error;
  }
}

export async function denyInvitation(id: string): Promise<{code: string}> {
  try {
    await axios.post(`/invitation/${id}/deny`);

    return {
      code: 'SUCCESS'
    };
  } catch (error) {
    throw error;
  }
}

export async function cancelInvitation(id: string): Promise<{code: string}> {
  try {
    await axios.post(`/invitation/${id}/cancel`);
    
    return {
      code: 'SUCCESS'
    };
  } catch (error) {
    throw error;
  }
}
