import axios from "axios";
import type { AxiosInstance, AxiosResponse, AxiosError } from "axios";

const axiosClient: AxiosInstance = axios.create({
  baseURL: `${import.meta.env.VITE_API_BASE_URL}/api`,
  withCredentials: true,
});

const csrfClient: AxiosInstance = axios.create({
  baseURL: `${import.meta.env.VITE_API_BASE_URL}`,
  withCredentials: true,
});

axiosClient.interceptors.response.use(
  (response: AxiosResponse) => response,
  (error: AxiosError) => {
    if (error.response) {
      switch (error.response.status) {
        case 401:
          console.warn("Unauthorized – maybe redirect to login");
          break;
        case 403:
          alert("You don't have permission to access this resource.");
          break;
        case 500:
          console.error("Server error:", error.response.data);
          break;
      }
    }
    return Promise.reject(error);
  }
);

export { axiosClient, csrfClient };
