import {http} from "./http";

export const fetchDashboard = async () => {
    return await http.get("/")
}
